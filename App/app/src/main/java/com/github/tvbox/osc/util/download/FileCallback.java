package com.github.tvbox.osc.util.download;

import com.lzy.okgo.callback.AbsCallback;

import java.io.File;

import okhttp3.Response;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public abstract class FileCallback extends AbsCallback<File> {

    private FileConvert convert;    //文件转换类

    public FileCallback() {
        this(null);
    }

    public FileCallback(String destFileName) {
        this(null, destFileName);
    }

    public FileCallback(String destFileDir, String destFileName) {
        convert = new FileConvert(destFileDir, destFileName);
        convert.setCallback(this);
    }

    @Override
    public File convertResponse(Response response) throws Throwable {
        File file = convert.convertResponse(response);
        response.close();
        return file;
    }

    @Override
    public void onError(com.lzy.okgo.model.Response<File> response) {
        super.onError(response);
        convert.onError();
    }

    public void pause() {
        convert.pause();
    }

    public void resume() {
        convert.resume();
    }

    public boolean isPause() {
        return convert.isPause();
    }
}
