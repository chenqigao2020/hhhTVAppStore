package com.github.tvbox.osc.ui.adapter;

import android.widget.ImageView;
import com.bumptech.glide.Glide;
import com.chad.library.adapter.base.BaseQuickAdapter;
import com.chad.library.adapter.base.BaseViewHolder;
import com.github.tvbox.osc.R;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class DetailRvAdapter extends BaseQuickAdapter<String, BaseViewHolder> {
    public DetailRvAdapter() {
        super(R.layout.detail_rv_item_layout, null);
    }


    @Override
    protected void convert(BaseViewHolder holder, String item) {
        Glide.with(mContext).load(item).into((ImageView) holder.getView(R.id.detail_item_img));
    }
}
