package com.github.tvbox.osc.ui.adapter;

import android.widget.ImageView;

import com.bumptech.glide.Glide;
import com.chad.library.adapter.base.BaseQuickAdapter;
import com.chad.library.adapter.base.BaseViewHolder;
import com.github.tvbox.osc.R;
import com.github.tvbox.osc.bean.App;

import java.util.ArrayList;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class ClassifyListAdapter extends BaseQuickAdapter<App, BaseViewHolder> {

    public ClassifyListAdapter() {
        super(R.layout.item_apps, new ArrayList<>());
    }

    @Override
    protected void convert(BaseViewHolder helper, App item) {
        helper.setText(R.id.appName, item.name);
        helper.setText(R.id.appmb, "大小：" + item.size);
        ImageView ivApps = helper.getView(R.id.ivApps);
        Glide.with(this.mContext).load(item.icon).into(ivApps);
        helper.setText(R.id.detailsTextView, item.details);
    }
}
