package com.github.tvbox.osc.util;

import android.content.Context;

import com.github.tvbox.osc.R;
import com.github.tvbox.osc.base.App;
import com.github.tvbox.osc.util.download.FileCallback;
import com.lzy.okgo.OkGo;
import com.lzy.okgo.model.Progress;
import com.lzy.okgo.model.Response;
import com.lzy.okgo.utils.HttpUtils;

import java.io.File;
import java.security.MessageDigest;
import java.util.HashMap;
import java.util.Map;

import static com.github.tvbox.osc.util.ToolUtils.showToast;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class AppDetailDownloadManager {

    private static AppDetailDownloadManager manager;

    private AppDetailDownloadManager() {}

    private final Map<String, DownloadApp> map = new HashMap<>();

    public interface OnListener {
        void onError();
        void onSuccess(String filePath);
        void onProgressChange(int progress);
    }

    public static AppDetailDownloadManager getInstance() {
        if (manager == null) {
            manager = new AppDetailDownloadManager();
        }
        return manager;
    }

    public void start(Context context, String url, OnListener onListener) {
        DownloadApp downloadApp = map.get(url);
        if (downloadApp != null) {
            return;
        }
        downloadApp = new DownloadApp(url);
        downloadApp.setContext(context);
        downloadApp.setOnListener(onListener);
        map.put(url, downloadApp);
        downloadApp.start();
    }

    public void resume(Context context, String url, OnListener onListener) {
        DownloadApp downloadApp = map.get(url);
        if (downloadApp == null) {
            return;
        }
        downloadApp.setContext(context);
        downloadApp.setOnListener(onListener);
        downloadApp.resume();
    }

    public void set(Context context, String url, OnListener onListener) {
        DownloadApp downloadApp = map.get(url);
        if (downloadApp == null) {
            return;
        }
        downloadApp.setContext(context);
        downloadApp.setOnListener(onListener);
    }

    public void pause(String url) {
        DownloadApp downloadApp = map.get(url);
        if (downloadApp == null) {
            return;
        }
        downloadApp.pause();
    }

    public boolean isInstalled(String appPackageName) {
        try {
            App.getAppContext().getPackageManager().getApplicationInfo(appPackageName, 0);
            return true;
        } catch (Throwable e) {
            e.printStackTrace();
        }
        return false;
    }

    public boolean isDownloaded(String url) {
        return new File(getApkFilePath(url)).exists();
    }

    public boolean isDownloading(String url) {
        DownloadApp downloadApp = map.get(url);
        return downloadApp != null && !downloadApp.isPause();
    }

    public boolean isPause(String url) {
        DownloadApp downloadApp = map.get(url);
        return downloadApp != null && downloadApp.isPause();
    }

    class DownloadApp {

        private String url;
        private Context context;
        private OnListener onListener;
        private FileCallback fileCallback;

        public DownloadApp(String url) {
            this.url = url;
        }

        public String getUrl() {
            return url;
        }

        public void setContext(Context context) {
            this.context = context;
        }

        public void setOnListener(OnListener onListener) {
            this.onListener = onListener;
        }

        public boolean isPause() {
            return fileCallback != null && fileCallback.isPause();
        }

        public void pause() {
            if (fileCallback == null) {
                return;
            }
            fileCallback.pause();
        }

        public void resume() {
            if (fileCallback == null) {
                return;
            }
            fileCallback.resume();
        }

        public void start() {
            String saveDir = getSaveDirPath();
            String tempFileName = getTempApkFileName(url);
            fileCallback = new FileCallback(saveDir, tempFileName) {
                @Override
                public void onSuccess(Response<File> response) {
                    String tempFilePath = saveDir + "/" + tempFileName;
                    String apkPath = getApkFilePath(url);
                    rename(tempFilePath, apkPath, new Runnable() {
                        @Override
                        public void run() {
                            complete();
                            if (onListener != null) {
                                onListener.onSuccess(apkPath);
                            }
                        }
                    });
                }

                @Override
                public void onError(Response<File> response) {
                    super.onError(response);
                    complete();
                    if (onListener != null) {
                        onListener.onError();
                    }
                }

                @Override
                public void downloadProgress(Progress p) {
                    super.downloadProgress(p);
                    int progress = (int) (p.currentSize * 1f / p.totalSize * 100);
                    if (onListener != null) {
                        onListener.onProgressChange(progress);
                    }
                }

                public void complete() {
                    map.remove(url);
                }
            };
            OkGo.<File>get(url).execute(fileCallback);
        }

        private void rename(String tempPath, String newPath, Runnable runnable) {
            new Thread() {
                @Override
                public void run() {
                    super.run();
                    File downloadFile = new File(tempPath);
                    downloadFile.renameTo(new File(newPath));
                    HttpUtils.runOnUiThread(runnable);
                }
            }.start();
        }
    }

    private String getSaveDirPath() {
        File file = App.getAppContext().getExternalCacheDir();
        String savePath = file != null ? file.getAbsolutePath() : App.getAppContext().getCacheDir().getAbsolutePath();
        return savePath;
    }

    public String getTempApkFileName(String url) {
        return "downloading_" + md5(url) + ".apk";
    }

    public String getApkFilePath(String url) {
        return getSaveDirPath() + "/" + md5(url) + ".apk";
    }

    private final char HEX_DIGITS[] = { '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F' };

    private String toHexString(byte[] b) {
        //String to byte
        StringBuilder sb = new StringBuilder(b.length * 2);
        for (int i = 0; i < b.length; i++) {
            sb.append(HEX_DIGITS[(b[i] & 0xf0) >>> 4]);
            sb.append(HEX_DIGITS[b[i] & 0x0f]);
        }
        return sb.toString();
    }

    private String md5(String s) {
        try {
            // Create MD5 Hash
            MessageDigest digest = java.security.MessageDigest.getInstance("MD5");
            digest.update(s.getBytes());
            byte messageDigest[] = digest.digest();
            return toHexString(messageDigest);
        } catch (Throwable e) {
            e.printStackTrace();
        }
        return "";
    }

}
