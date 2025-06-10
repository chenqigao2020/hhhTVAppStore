package com.github.tvbox.osc.util.load;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.viewbinding.ViewBinding;

import com.github.tvbox.osc.databinding.EmptyViewBinding;
import com.github.tvbox.osc.databinding.ErrorViewBinding;
import com.github.tvbox.osc.databinding.LoadingViewBinding;

import java.util.ArrayList;
import java.util.List;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class TVLoadController implements ILoadController {

    private final ViewGroup rootLayout;

    private Runnable loadRunnable;

    public TVLoadController(ViewGroup rootLayout) {
        this.rootLayout = rootLayout;
    }

    @Override
    public void setLoadTask(Runnable loadRunnable) {
        this.loadRunnable = loadRunnable;
    }

    @Override
    public void showLoading() {
        reset();
        LoadingViewBinding binding = LoadingViewBinding.inflate(
                LayoutInflater.from(rootLayout.getContext()),
                rootLayout,
                false
        );
        load(binding);
    }

    @Override
    public void showSuccess() {
        reset();
        for (int i = 0 ; i < rootLayout.getChildCount() ; i ++) {
            View view = rootLayout.getChildAt(i);
            view.setVisibility(View.VISIBLE);
        }
    }

    @Override
    public void showError(View nextFocusView) {
        reset();
        ErrorViewBinding binding = ErrorViewBinding.inflate(
                LayoutInflater.from(rootLayout.getContext()),
                rootLayout,
                false
        );
        binding.tryButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (nextFocusView != null) {
                    nextFocusView.requestFocus();
                }
                if (loadRunnable != null) {
                    loadRunnable.run();
                }
            }
        });
        load(binding);
    }

    @Override
    public void showEmpty(View nextFocusView) {
        reset();
        EmptyViewBinding binding = EmptyViewBinding.inflate(
                LayoutInflater.from(rootLayout.getContext()),
                rootLayout,
                false
        );
        binding.refreshButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (nextFocusView != null) {
                    nextFocusView.requestFocus();
                }
                if (loadRunnable != null) {
                    loadRunnable.run();
                }
            }
        });
        load(binding);
    }

    private void load(ViewBinding binding) {
        rootLayout.addView(binding.getRoot());
        hideOtherView(binding);
    }

    private void hideOtherView(ViewBinding binding) {
        for (int i = 0 ; i < rootLayout.getChildCount() ; i ++) {
            View view = rootLayout.getChildAt(i);
            view.setVisibility(view == binding.getRoot() ? View.VISIBLE : View.INVISIBLE);
        }
    }

    private void reset() {
        while (rootLayout.getChildCount() > 1) {
            rootLayout.removeViewAt(rootLayout.getChildCount() - 1);
        }
    }

}
