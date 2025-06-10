package com.github.tvbox.osc.util.http;

import android.text.TextUtils;

import com.github.tvbox.osc.util.BaseR;

import java.io.IOException;

import okhttp3.Headers;
import okhttp3.Interceptor;
import okhttp3.Request;
import okhttp3.Response;
import okhttp3.ResponseBody;
import okio.GzipSource;
import okio.Okio;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class DecryptInterceptor implements Interceptor {

    /*@Override
    public Response intercept(Chain chain) throws IOException {
        Request request = chain.request();
        Response response = chain.proceed(request);
        return response;
    }*/

    @Override
    public Response intercept(Chain chain) throws IOException {
        Request request = chain.request();
        Response response = chain.proceed(request);
        ResponseBody responseBody = response.body();
        if (responseBody == null) {
            return response;
        }
        if (isDownload(response)) {
            return response;
        }
        Headers headers = response.headers();
        byte[] bytes;
        if (isGzipped(response)) {
            GzipSource gzipSource = new GzipSource(responseBody.source());
            bytes = Okio.buffer(gzipSource).readByteArray();
            headers = headers.newBuilder()
                    .removeAll("Content-Encoding")
                    .build();
        } else {
            bytes = responseBody.source().readByteArray();
        }
        String body = new String(bytes);
        String newBody = BaseR.decry_R(body);
        if (TextUtils.isEmpty(newBody)) {
            ResponseBody newResponseBody = ResponseBody.create(responseBody.contentType(), bytes);
            Response newResponse = new Response
                    .Builder()
                    .request(request)
                    .protocol(response.protocol())
                    .code(response.code())
                    .message(response.message())
                    .headers(headers)
                    .body(newResponseBody)
                    .build();
            return newResponse;
        }
        ResponseBody newResponseBody = ResponseBody.create(responseBody.contentType(), newBody);
        Response newResponse = new Response
                .Builder()
                .request(request)
                .protocol(response.protocol())
                .code(response.code())
                .message(response.message())
                .headers(headers)
                .body(newResponseBody)
                .build();
        return newResponse;
    }

    private byte[] getBytes(Response response) throws IOException {
        if (isGzipped(response)) {
            GzipSource gzipSource = new GzipSource(response.body().source());
            byte[] bytes = Okio.buffer(gzipSource).readByteArray();
            return bytes;
        } else {
            byte[] bytes = response.body().source().readByteArray();
            return bytes;
        }
    }

    private Boolean isGzipped(Response response) {
        String encoding = response.header("Content-Encoding");
        return "gzip".equals(encoding);
    }

    private boolean isDownload(Response response) {
        String value = response.header("Content-Length");
        return value != null;
    }

}
