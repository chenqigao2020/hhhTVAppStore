package com.github.tvbox.osc.util.load;

import android.view.View;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public interface ILoadController {

    // 设置加载的代码块
    void setLoadTask(Runnable loadRunnable);

    // 显示加载状态
    void showLoading();

    // 显示加载成功（隐藏所有状态）
    void showSuccess();

    /**
     *
     * 显示异常
     * nextFocusView 下一个获取焦点的View
     */
    void showError(View nextFocusView);

    /**
     *
     * 显示空内容
     * nextFocusView 下一个获取焦点的View
     */
    void showEmpty(View nextFocusView);

}
