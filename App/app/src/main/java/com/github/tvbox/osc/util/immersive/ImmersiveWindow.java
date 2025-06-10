package com.github.tvbox.osc.util.immersive;

import android.graphics.Color;
import android.os.Build;
import android.view.View;
import android.view.Window;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class ImmersiveWindow implements IImmersiveWindow {

    private final IImmersiveWindow immersiveWindow;

    private final Window window;

    public ImmersiveWindow(Window window) {
        this.window = window;
        if (isSupportImmersiveWindow()) {
            immersiveWindow = new Android8ImmersiveWindow(window);
        } else {
            immersiveWindow = new Android7ImmersiveWindow();
        }
    }

    /**
     * 是否支持沉浸式窗口。
     * 实际上安卓6.0已经支持，但存在Activity无法正确处理键盘弹出后的高度调整，该问题于安卓8.0修复，所以这里认为安卓8.0才开始支持（完美支持）
     * **/
    public boolean isSupportImmersiveWindow(){
        return Build.VERSION.SDK_INT >= Build.VERSION_CODES.O;
    }

    /**
     * 给定一个颜色值，返回于其可以搭配的布尔值
     * true 亮色
     * false 暗色
     * **/
    public boolean isDark(int color){
        int blue = Color.blue(color);
        int green = Color.green(color);
        int red = Color.red(color);
        int toGrey =  (red * 38 + green * 75 + blue * 15) >> 7;
        boolean isDark = toGrey > 225;
        return isDark;
    }

    @Override
    public void setNavigation(boolean isShow) {
        View decorView = window.getDecorView();
        int flag = decorView.getSystemUiVisibility();
        if (isShow) {
            flag |= View.SYSTEM_UI_FLAG_VISIBLE;
        } else {
            flag |= View.SYSTEM_UI_FLAG_HIDE_NAVIGATION
                    | View.INVISIBLE;
        }
        flag |= View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY;
        decorView.setSystemUiVisibility(flag);
    }

    @Override
    public void setStatus(boolean isShow) {
        View decorView = window.getDecorView();
        int flag = decorView.getSystemUiVisibility();
        if (isShow) {
            flag |= View.SYSTEM_UI_FLAG_VISIBLE;
        } else {
            flag |= View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                    | View.INVISIBLE;
        }
        flag |= View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY;
        decorView.setSystemUiVisibility(flag);
    }

    @Override
    public void customizeFillSystemBar(OnWindowInsetsChangeListener listener) {
        immersiveWindow.customizeFillSystemBar(listener);
    }

    @Override
    public void updateWindow() {
        immersiveWindow.updateWindow();
    }

    @Override
    public void autoFillSystemBar() {
        immersiveWindow.autoFillSystemBar();
    }

    @Override
    public void setStatusIconColor(boolean isDark) {
        immersiveWindow.setStatusIconColor(isDark);
    }

    @Override
    public void setNavigationIconColor(boolean isDark) {
        immersiveWindow.setNavigationIconColor(isDark);
    }

}
