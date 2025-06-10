package com.github.tvbox.osc.base;

import android.app.Application;

import com.github.tvbox.osc.util.OkGoHelper;

import me.jessyan.autosize.AutoSizeConfig;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class App extends Application {

    private static App instance;

    @Override
    public void onCreate() {
        super.onCreate();
        instance = this;
        OkGoHelper.init();
        autoSize();
    }

    public static App getAppContext() {
        return instance;
    }

    private void autoSize() {
        int width = getResources().getDisplayMetrics().widthPixels;
        int height = getResources().getDisplayMetrics().heightPixels;
        if (height > width) {
            int temp = width;
            width = height;
            height = temp;
        }
        AutoSizeConfig.getInstance()
                .setScreenWidth(width)
                .setScreenHeight(height)
                .setBaseOnWidth(true)
                .setDesignWidthInDp(720)
                .getUnitsManager()
                .setSupportDP(true);
    }

}