package com.github.tvbox.osc.util.http;

import android.text.TextUtils;

import com.github.tvbox.osc.util.HawkConfig;
import com.github.tvbox.osc.util.MD5;
import com.lzy.okgo.request.base.ProgressRequestBody;

import java.io.IOException;
import java.lang.reflect.Field;
import java.net.URLDecoder;
import java.util.List;

import okhttp3.FormBody;
import okhttp3.Interceptor;
import okhttp3.MultipartBody;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;
import okio.Buffer;
import okio.BufferedSink;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class SignInterceptor implements Interceptor {

    @Override
    public Response intercept(Chain chain) throws IOException {
        Request originalRequest = chain.request();
        RequestBody requestBody = originalRequest.body();
        if (requestBody == null) {
            return chain.proceed(originalRequest);
        }
        Request newRequest = newRequest(originalRequest);
        if (newRequest == null) {
            newRequest = originalRequest;
        }
        return chain.proceed(newRequest);
    }

    private Request newRequest(Request originalRequest) throws IOException {
        RequestBody originalRequestBody = originalRequest.body();
        if (originalRequestBody == null) {
            return originalRequest;
        }
        if (!(originalRequestBody instanceof ProgressRequestBody)) {
            return originalRequest;
        }
        ProgressRequestBody<?> progressRequestBody = (ProgressRequestBody<?>) originalRequestBody;
        RequestBody requestBody = null;
        try {
            Field field = ProgressRequestBody.class.getDeclaredField("requestBody");
            field.setAccessible(true);
            requestBody = (RequestBody) field.get(progressRequestBody);
        } catch (Throwable throwable) {
            throwable.printStackTrace();
        }
        if (requestBody instanceof FormBody) {
            return newFormRequest(originalRequest);
        }
        if (requestBody instanceof MultipartBody) {
            MultipartBody body = (MultipartBody) requestBody;
            return newFileFormRequest(originalRequest, body);
        }
        return null;
    }

    // 处理文件表单
    private Request newFileFormRequest(Request originalRequest, MultipartBody multipartBody) throws IOException {
        RequestBody originalRequestBody = originalRequest.body();
        if (originalRequestBody == null) {
            return originalRequest;
        }
        List<MultipartBody.Part> parts = multipartBody.parts();
        StringBuilder stringBuffer = new StringBuilder();
        for (int i = 0 ; i < parts.size() ; i ++) {
            MultipartBody.Part part = parts.get(i);
            if (part.body().contentType() != null) {
                continue;
            }
            String key = part.headers() != null ? part.headers().value(0) : "";
            key = key.split("; ")[1];
            key = key.split("=")[1];
            key = key.replaceAll("\"", "");
            String value = readBody(part.body());
            if (stringBuffer.length() > 0) {
                stringBuffer.append("&");
            }
            stringBuffer.append(key);
            stringBuffer.append("=");
            stringBuffer.append(value);
        }
        MultipartBody.Part timePart = MultipartBody.Part.createFormData("t", String.valueOf(System.currentTimeMillis() / 1000));
        MultipartBody.Part signPart = MultipartBody.Part.createFormData("sign", setSign(stringBuffer.toString()));
        MultipartBody.Builder builder = new MultipartBody
                .Builder()
                .setType(multipartBody.type());
        for (int i = 0 ; i < parts.size() ; i ++) {
            MultipartBody.Part part = parts.get(i);
            if (part.body().contentType() != null) {
                continue;
            }
            builder.addPart(parts.get(i));
        }
        builder.addPart(timePart);
        builder.addPart(signPart);
        for (int i = 0 ; i < parts.size() ; i ++) {
            MultipartBody.Part part = parts.get(i);
            if (part.body().contentType() == null) {
                continue;
            }
            builder.addPart(parts.get(i));
        }
        MultipartBody newMultipartBody = builder.build();
        Request newRequest = originalRequest
                .newBuilder()
                .post(newMultipartBody)
                .build();
        return newRequest;
    }

    // 处理简单表单
    private Request newFormRequest(Request originalRequest) throws IOException {
        RequestBody originalRequestBody = originalRequest.body();
        if (originalRequestBody == null) {
            return originalRequest;
        }
        String body = readBody(originalRequestBody);
        body = URLDecoder.decode(body);
        String oldBody = body;
        if (!TextUtils.isEmpty(body)) {
            body += "&";
        }
        body += "t=" + System.currentTimeMillis() / 1000;
        body += "&sign=" + setSign(TextUtils.isEmpty(oldBody) ? "null" : oldBody);
        RequestBody newRequestBody = RequestBody.create(originalRequestBody.contentType(), body);
        Request newRequest = originalRequest
                .newBuilder()
                .post(newRequestBody)
                .build();
        return newRequest;
    }

    private String readBody(RequestBody requestBody) throws IOException {
        BufferedSink sink = new Buffer();
        requestBody.writeTo(sink);;
        String body = sink.buffer().readUtf8();
        sink.close();
        return body;
    }

    /**
     * 处理加密
     **/
    public static String setSign(String data) {
        String signData;
        if (data.equals("null")) {
            signData = "t=" + System.currentTimeMillis() / 1000 + "&" + HawkConfig.API_KEY;
        } else {
            signData = data + "&t=" + System.currentTimeMillis() / 1000 + "&" + HawkConfig.API_KEY;
        }
        return MD5.encode(signData);
    }
}
