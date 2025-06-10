package com.github.tvbox.osc.util.immersive;

import android.view.WindowInsets;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public interface IImmersiveWindow {

    // 设置导航栏的显示和隐藏
    void setNavigation(boolean isShow);

    // 设置状态栏的显示和隐藏
    void setStatus(boolean isShow);

    /**
     * 以自定义的方式填充状态栏和导航栏区域，防止被布局内容覆盖
     * **/
    void customizeFillSystemBar(OnWindowInsetsChangeListener listener);

    /**
     * 把窗口改为沉浸式窗口
     * **/
    void updateWindow();

    /**
     * 自动填充状态栏和导航栏区域，防止被布局内容覆盖
     * **/
    void autoFillSystemBar();

    /**
     * 设置状态栏图标颜色风格
     * setDark true 亮色
     * setDark false 暗色
     * **/
    void setStatusIconColor(boolean isDark);

    /**
     * 设置导航按键图标颜色风格
     * setDark true 亮色
     * setDark false 暗色
     * **/
    void setNavigationIconColor(boolean isDark);

    interface OnWindowInsetsChangeListener {

        void onChange(WindowInsets insets);

    }

}
