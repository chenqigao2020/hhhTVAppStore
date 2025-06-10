package com.github.tvbox.osc.util;

import android.app.Application;
import android.os.Build;
import android.os.Environment;

import androidx.annotation.Nullable;

import com.lzy.okgo.OkGo;
import com.lzy.okgo.callback.FileCallback;
import com.lzy.okgo.model.Progress;
import com.lzy.okgo.model.Response;

import java.io.File;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class AppDownloadManager {

    private static AppDownloadManager manager;

    private AppDownloadManager() {}

    public static AppDownloadManager getInstance(Application app) {
        if (manager == null) {
            manager = new AppDownloadManager();
            manager.app = app;
        }
        return manager;
    }

    private Application app;

    private String currentUrl;

    private String getDir() {
        File file = app.getExternalCacheDir();
        if (file != null) {
            return file.getAbsolutePath();
        }
        return null;
    }

    // 执行下载安装，
    public boolean download(String url, OnDownloadListener listener) {
        if (isDownloadIng()) {
            return false;
        }
        String dir = getDir();
        if (dir == null) {
            listener.onFail("找不到储存文件夹");
            return true;
        }
        this.currentUrl = url;
        OkGo.<File>get(url).execute(new FileCallback(dir, System.currentTimeMillis() + ".apk") {
            @Override
            public void onSuccess(Response<File> response) {
                String filePath = response.body().getAbsolutePath();
                currentUrl = null;
                listener.onSuccess(filePath);
            }

            @Override
            public void onError(Response<File> response) {
                super.onError(response);
                currentUrl = null;
                listener.onFail("应用下载失败，请检查网络");
            }

            @Override
            public void downloadProgress(Progress progress) {
                super.downloadProgress(progress);
            }
        });
        return true;
    }

    // 是否正在安装中
    public boolean isDownloadIng() {
        return currentUrl != null;
    }

    public interface OnDownloadListener {
        void onSuccess(String path);
        void onFail(String error);
    }

}
