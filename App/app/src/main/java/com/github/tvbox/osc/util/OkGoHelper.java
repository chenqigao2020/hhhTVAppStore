package com.github.tvbox.osc.util;

import com.github.tvbox.osc.util.http.DecryptInterceptor;
import com.github.tvbox.osc.util.http.SignInterceptor;
import com.lzy.okgo.OkGo;
import com.lzy.okgo.model.HttpHeaders;

import java.util.concurrent.TimeUnit;

import okhttp3.OkHttpClient;
import okhttp3.internal.Version;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class OkGoHelper {
    public static final long DEFAULT_MILLISECONDS = 8000;      //默认的超时时间

    static void initExoOkHttpClient() {
        OkHttpClient.Builder builder = new OkHttpClient.Builder();
        builder.retryOnConnectionFailure(true);
        builder.followRedirects(true);
        builder.followSslRedirects(true);
    }

    static OkHttpClient defaultClient = null;
    static OkHttpClient noRedirectClient = null;

    public static void init() {
        OkHttpClient.Builder builder = new OkHttpClient.Builder();

        builder.readTimeout(DEFAULT_MILLISECONDS, TimeUnit.MILLISECONDS);
        builder.writeTimeout(DEFAULT_MILLISECONDS, TimeUnit.MILLISECONDS);
        builder.connectTimeout(DEFAULT_MILLISECONDS, TimeUnit.MILLISECONDS);

        HttpHeaders.setUserAgent(Version.userAgent());

        builder.addInterceptor(new SignInterceptor());
        builder.addNetworkInterceptor(new DecryptInterceptor());

        OkHttpClient okHttpClient = builder.build();
        OkGo.getInstance().setOkHttpClient(okHttpClient);

        defaultClient = okHttpClient;

        builder.followRedirects(false);
        builder.followSslRedirects(false);
        noRedirectClient = builder.build();

        initExoOkHttpClient();
    }

}
