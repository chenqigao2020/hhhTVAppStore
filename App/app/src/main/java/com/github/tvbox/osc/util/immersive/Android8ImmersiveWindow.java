package com.github.tvbox.osc.util.immersive;

import android.graphics.Color;
import android.os.Build;
import android.view.View;
import android.view.Window;
import android.view.WindowInsets;
import android.view.WindowManager;

import androidx.annotation.NonNull;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class Android8ImmersiveWindow implements IImmersiveWindow {

    private final Window window;

    public Android8ImmersiveWindow(Window window) {
        this.window = window;
    }

    @Override
    public void setNavigation(boolean isShow) {

    }

    @Override
    public void setStatus(boolean isShow) {

    }

    @Override
    public void customizeFillSystemBar(OnWindowInsetsChangeListener listener) {
        window.getDecorView().setOnApplyWindowInsetsListener(new View.OnApplyWindowInsetsListener() {
            @NonNull
            @Override
            public WindowInsets onApplyWindowInsets(@NonNull View v, @NonNull WindowInsets insets) {
                if (listener != null) {
                    listener.onChange(insets);
                    return insets;
                }
                return insets;
            }
        });
    }

    @Override
    public void updateWindow() {
        View decorView = window.getDecorView();
        // 修复沉浸式的Dialog会出现状态栏、导航栏黑色背景的问题
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        // 状态栏沉浸
        int options = View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN;
        window.setStatusBarColor(Color.TRANSPARENT);
        //导航栏沉浸
        options |= View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION;
        window.setNavigationBarColor(Color.TRANSPARENT);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            window.setNavigationBarContrastEnforced(false);
        }
        decorView.setSystemUiVisibility(options);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.P) {
            WindowManager.LayoutParams lp = window.getAttributes();
            lp.layoutInDisplayCutoutMode = WindowManager.LayoutParams.LAYOUT_IN_DISPLAY_CUTOUT_MODE_SHORT_EDGES;
            window.setAttributes(lp);
        }
    }

    @Override
    public void autoFillSystemBar() {
        window.getDecorView().setOnApplyWindowInsetsListener(new View.OnApplyWindowInsetsListener() {
            @NonNull
            @Override
            public WindowInsets onApplyWindowInsets(@NonNull View v, @NonNull WindowInsets insets) {
                v.setPadding(
                        0,
                        insets.getSystemWindowInsetTop(),
                        0,
                        insets.getSystemWindowInsetBottom()
                );
                return insets;
            }
        });
    }

    @Override
    public void setStatusIconColor(boolean isDark) {
        View decorView = window.getDecorView();
        int vis = decorView.getSystemUiVisibility();
        if(isDark){
            vis |= View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR;
        } else{
            vis &= ~View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR;
        }
        decorView.setSystemUiVisibility(vis);
    }

    @Override
    public void setNavigationIconColor(boolean isDark) {
        View decorView = window.getDecorView();
        int vis = decorView.getSystemUiVisibility();
        if(isDark){
            vis |= View.SYSTEM_UI_FLAG_LIGHT_NAVIGATION_BAR;
        } else{
            vis &= ~View.SYSTEM_UI_FLAG_LIGHT_NAVIGATION_BAR;
        }
        decorView.setSystemUiVisibility(vis);
    }

}
