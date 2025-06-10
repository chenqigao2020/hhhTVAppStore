package com.github.tvbox.osc.util.download;

import android.os.Environment;
import android.text.TextUtils;

import com.lzy.okgo.callback.Callback;
import com.lzy.okgo.convert.Converter;
import com.lzy.okgo.model.Progress;
import com.lzy.okgo.utils.HttpUtils;
import com.lzy.okgo.utils.IOUtils;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;

import okhttp3.Response;
import okhttp3.ResponseBody;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class FileConvert implements Converter<File> {

    public static final String DM_TARGET_FOLDER = File.separator + "download" + File.separator; //下载目标文件夹

    private String folder;                  //目标文件存储的文件夹路径
    private String fileName;                //目标文件存储的文件名
    private Callback<File> callback;        //下载回调
    private boolean isPause = false;

    public FileConvert() {
        this(null);
    }

    public FileConvert(String fileName) {
        this(Environment.getExternalStorageDirectory() + DM_TARGET_FOLDER, fileName);
    }

    public FileConvert(String folder, String fileName) {
        this.folder = folder;
        this.fileName = fileName;
    }

    public void setCallback(Callback<File> callback) {
        this.callback = callback;
    }

    @Override
    public File convertResponse(Response response) throws Throwable {
        String url = response.request().url().toString();
        if (TextUtils.isEmpty(folder)) folder = Environment.getExternalStorageDirectory() + DM_TARGET_FOLDER;
        if (TextUtils.isEmpty(fileName)) fileName = HttpUtils.getNetFileName(response, url);

        File dir = new File(folder);
        IOUtils.createFolder(dir);
        File file = new File(dir, fileName);
        IOUtils.delFileOrFolder(file);

        InputStream bodyStream = null;
        byte[] buffer = new byte[8192];
        FileOutputStream fileOutputStream = null;
        try {
            ResponseBody body = response.body();
            if (body == null) return null;

            bodyStream = body.byteStream();
            Progress progress = new Progress();
            progress.totalSize = body.contentLength();
            progress.fileName = fileName;
            progress.filePath = file.getAbsolutePath();
            progress.status = Progress.LOADING;
            progress.url = url;
            progress.tag = url;

            int len;
            fileOutputStream = new FileOutputStream(file);
            while ((len = bodyStream.read(buffer)) != -1) {
                fileOutputStream.write(buffer, 0, len);

                if (callback == null) continue;
                Progress.changeProgress(progress, len, new Progress.Action() {
                    @Override
                    public void call(Progress progress) {
                        onProgress(progress);
                    }
                });
                while (isPause) {
                    Thread.sleep(2000);
                }
            }
            fileOutputStream.flush();
            return file;
        } finally {
            IOUtils.closeQuietly(bodyStream);
            IOUtils.closeQuietly(fileOutputStream);
        }
    }

    private void onProgress(final Progress progress) {
        HttpUtils.runOnUiThread(new Runnable() {
            @Override
            public void run() {
                callback.downloadProgress(progress);   //进度回调的方法
            }
        });
    }

    public void pause() {
        isPause = true;
    }

    public void resume() {
        isPause = false;
    }

    public boolean isPause() {
        return isPause;
    }

    public void onError() {
        isPause = false;
    }

}
