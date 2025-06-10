package com.github.tvbox.osc.util;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.graphics.PixelFormat;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.os.Build;
import android.os.Environment;
import android.os.Handler;
import android.os.Looper;
import android.provider.Settings;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.content.FileProvider;
import androidx.fragment.app.FragmentActivity;

import com.github.tvbox.osc.R;
import com.github.tvbox.osc.base.App;
import com.lzy.okgo.model.Response;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class ToolUtils {
    private static final String TAG = "ToolUtils";

    public static String setApi(String act) {
        String url = HawkConfig.MMM_MMM + "/api.php?act=" + act;
        url += "&app=" + HawkConfig.APP_ID;
        return url;
    }

    public static void downloadInstallApp(FragmentActivity activity, String url) {
        boolean isSuccess = AppDownloadManager.getInstance(App.getAppContext()).download(url, new AppDownloadManager.OnDownloadListener() {
            @Override
            public void onSuccess(String path) {
                if (activity.getSupportFragmentManager().isDestroyed()) {
                    return;
                }
                ToolUtils.installApk(activity, path);
            }

            @Override
            public void onFail(String error) {
                if (activity.getSupportFragmentManager().isDestroyed()) {
                    return;
                }
                ToolUtils.showToast(activity, error);
            }
        });
        if (isSuccess) {
            ToolUtils.showToast(activity, "正在后台下载,请稍后");
        } else {
            ToolUtils.showToast(activity, "当前有应用正在下载，请稍后重试");
        }
    }

    /**
     * 获取当前应用包名
     *
     * @return
     */
    public static String getCurrentPackageName(Context context) {
        PackageManager pm = context.getPackageManager();
        try {
            PackageInfo info = pm.getPackageInfo(context.getPackageName(), 0);
            return info.packageName;
        } catch (Exception e) {
            e.printStackTrace();
            return "";
        }
    }

    /**
     * 判断apk文件是否可以安装
     *
     * @param context
     * @param filePath
     * @return
     */
    public static boolean getUninatllApkInfo(Context context, String filePath) {
        boolean result = false;
        try {
            PackageManager pm = context.getPackageManager();
            PackageInfo info = pm.getPackageArchiveInfo(filePath, PackageManager.GET_ACTIVITIES);
            if (info != null) {
                result = true;
            }
        } catch (Exception e) {
            result = false;
            Log.e("zhouchuan", "*****  解析未安装的 apk 出现异常 *****" + e.getMessage(), e);
        }
        return result;
    }

    /**
     * 安装apk文件
     *
     * @param fileName
     * @author drowtram
     */
    public static void installApk(Context context, String fileName) {
        if (getUninatllApkInfo(context, fileName)) {
            Intent intent = new Intent(Intent.ACTION_VIEW);
            File apkFile = new File(fileName);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                Uri uri = FileProvider.getUriForFile(context, context.getPackageName() + ".fileprovider", apkFile);
                context.grantUriPermission(getCurrentPackageName(context), uri, Intent.FLAG_GRANT_READ_URI_PERMISSION);
                intent.setFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION);
                intent.setDataAndType(uri, "application/vnd.android.package-archive");
                context.startActivity(intent);
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                    boolean hasInstallPermission = context.getPackageManager().canRequestPackageInstalls();
                    if (!hasInstallPermission) {
                        Intent intent1 = new Intent(Settings.ACTION_MANAGE_UNKNOWN_APP_SOURCES);
                        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                        context.startActivity(intent1);
                        return;
                    }
                }
            } else {
                Uri uri = Uri.fromFile(apkFile);
                intent.addFlags(Intent.FLAG_GRANT_PERSISTABLE_URI_PERMISSION);
                intent.setDataAndType(uri, "application/vnd.android.package-archive");
                context.startActivity(intent);
            }
        } else {
            ToolUtils.showToast(context, "安装包解析失败，请稍后重试");
        }
    }

    /**
     * 获取当前应用版本号
     *
     * @return
     */
    @SuppressWarnings("unused")
    public static String getVersion(Context context) {
        PackageManager pm = context.getPackageManager();
        try {
            PackageInfo info = pm.getPackageInfo(context.getPackageName(), 0);
            return info.versionName;
        } catch (Exception e) {
            e.printStackTrace();
            return "";
        }
    }

    /**
     * 自定义Toast
     *
     * @param context
     * @param text
     */
    public static void showToast(Context context, String text) {
        Toast.makeText(context, text, Toast.LENGTH_SHORT).show();
    }

    public static boolean getIsEmpty(String text) {
        return text != null && !text.equals("") && text.length() >= 1; //true
    }

    public static String getAndroidId(Context context) {
        return Settings.System.getString(context.getContentResolver(), Settings.System.ANDROID_ID);
    }

    /*
     * 将时间转换为时间戳
     */
    @SuppressLint("SimpleDateFormat")
    public static int dateToStamp(String s) {
        int res = 0;
        try {
            SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            Date date = simpleDateFormat.parse(s);
            assert date != null;
            long ts = date.getTime() / 1000;
            res = Integer.parseInt(String.valueOf(ts));
        } catch (ParseException e) {
            e.printStackTrace();
        }
        return res;
    }

    public static String stampToDate(int s) {
        String res;
        @SuppressLint("SimpleDateFormat") SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        long lt = (long) s * 1000;

        if (lt == 999999999000L) {
            // 原始时间戳等于999999999时，返回永久会员
            return "永久会员";
        }

        if (lt == 888888888000L) {
            // 原始时间戳等于888888888时，返回免费模式
            return "免费模式";
        }
        // 时间戳大于当前时间，返回时间格式，否则返回未开通会员
        if (lt > System.currentTimeMillis()) {
            Date date = new Date(lt);
            res = simpleDateFormat.format(date);
            return res;
        }

        return "未开通会员";
    }

    /**
     * 初始化多线路数据
     **/
    public static boolean iniData2(Response<String> response, Context context) {
        try {
            String responseBody = response.body(); // 获取响应体的字符串
            Log.d(TAG, "请求结果：" + context + "====data:" + responseBody);

            JSONObject jo = new JSONObject(responseBody); // 创建 JSON 对象

            return true;

        } catch (JSONException e) {
            e.printStackTrace();
        }

        return false;
    }


    public static Bitmap drawableToBitmap(Drawable drawable) {

        // 获取 drawable 长宽
        int width = drawable.getIntrinsicWidth();
        int heigh = drawable.getIntrinsicHeight();

        drawable.setBounds(0, 0, width, heigh);

        // 获取drawable的颜色格式
        Bitmap.Config config = drawable.getOpacity() != PixelFormat.OPAQUE ? Bitmap.Config.ARGB_8888
                : Bitmap.Config.RGB_565;
        // 创建bitmap
        Bitmap bitmap = Bitmap.createBitmap(width, heigh, config);
        // 创建bitmap画布
        Canvas canvas = new Canvas(bitmap);
        // 将drawable 内容画到画布中
        drawable.draw(canvas);
        return bitmap;
    }
}

