package com.github.tvbox.osc.ui.activity;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.github.tvbox.osc.R;
import com.github.tvbox.osc.bean.App;
import com.github.tvbox.osc.ui.adapter.DetailRvAdapter;
import com.github.tvbox.osc.util.AppDetailDownloadManager;
import com.github.tvbox.osc.util.immersive.ImmersiveWindow;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import static com.github.tvbox.osc.util.ToolUtils.installApk;
import static com.github.tvbox.osc.util.ToolUtils.showToast;

/**
 * APP详情页
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class AppDetailActivity extends AppCompatActivity {

    private App bean;
    private Button mBt;
    private RecyclerView mRv;
    private DetailRvAdapter mAdapter;

    private AppDetailDownloadManager.OnListener onListener = new AppDetailDownloadManager.OnListener() {
        @Override
        public void onError() {
            Toast.makeText(getApplicationContext(), "下载失败", Toast.LENGTH_SHORT).show();
            onDownloadButtonTextChange();
        }

        @Override
        public void onSuccess(String filePath) {
            Toast.makeText(getApplicationContext(), "下载成功", Toast.LENGTH_SHORT).show();
            onDownloadButtonTextChange();
            installApk(AppDetailActivity.this, filePath);
        }

        @Override
        public void onProgressChange(int progress) {
            if (AppDetailDownloadManager.getInstance().isPause(bean.download)) {
                return;
            }
            mBt.setText("正在下载 " + progress + "%");
        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_app_detail);
        bean = (App) getIntent().getSerializableExtra("data");
        mRv = findViewById(R.id.detail_rv);
        mBt = findViewById(R.id.detail_bt);
        ImageView img = findViewById(R.id.detail_img);
        TextView content = findViewById(R.id.detail_tv_content);
        TextView name = findViewById(R.id.detail_tv_name);
        TextView appjs = findViewById(R.id.appjs);
        mBt.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                downloadApk();
            }
        });
        mRv.setLayoutManager(new LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false));
        mAdapter = new DetailRvAdapter();
        mRv.setAdapter(mAdapter);
        if (bean != null) {
            name.setText(bean.name);
            appjs.setText("              "+ bean.details);//简介
            Glide.with(this) // 传入 Context
                 .load(bean.icon) // 你要加载的图片 URL
                 .into(img); // 目标 ImageView 控件
            String contents = "大小：" + bean.size+"  |  " + "版本：" + bean.version;
            content.setText(contents);
            String[] parts = "".split("#");
            List<String> myList = new ArrayList<>(Arrays.asList(parts));
            mAdapter.setNewData(myList);
        }
    }

    private void immersiveWindow() {
        ImmersiveWindow immersiveWindow = new ImmersiveWindow(getWindow());
        immersiveWindow.updateWindow();
        immersiveWindow.setNavigation(false);
        immersiveWindow.setStatus(false);
    }

    @Override
    protected void onResume() {
        super.onResume();
        immersiveWindow();
        onDownloadButtonTextChange();
    }

    private void downloadApk() {
        AppDetailDownloadManager manager = AppDetailDownloadManager.getInstance();
        if (manager.isInstalled(bean.only)) {
            Intent intent = getPackageManager().getLaunchIntentForPackage(bean.only);
            if (intent != null) {
                startActivity(intent);
            }
            return;
        }
        if (manager.isDownloaded(bean.download)) {
            installApk(this, manager.getApkFilePath(bean.download));
            return;
        }
        if (manager.isDownloading(bean.download)) {
            manager.pause(bean.download);
            onDownloadButtonTextChange();
            return;
        }
        if (manager.isPause(bean.download)) {
            manager.resume(this, bean.download, onListener);
            onDownloadButtonTextChange();
            return;
        }
        showToast(this, "正在后台下载，完成后提示安装...");
        manager.start(this, bean.download, onListener);
        onDownloadButtonTextChange();
    }

    private void onDownloadButtonTextChange() {
        if (bean == null) {
            return;
        }
        AppDetailDownloadManager manager = AppDetailDownloadManager.getInstance();
        if (manager.isInstalled(bean.only)) {
            mBt.setText("打开");
            return;
        }
        if (manager.isDownloaded(bean.download)) {
            mBt.setText("已下载");
            return;
        }
        if (manager.isDownloading(bean.download)) {
            mBt.setText("下载中");
            manager.set(this, bean.download, onListener);
            return;
        }
        if (manager.isPause(bean.download)) {
            mBt.setText("已暂停");
            return;
        }
        mBt.setText("下  载");
    }

}
