package com.github.tvbox.osc.ui.activity;

import android.content.Intent;
import android.graphics.Rect;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowInsets;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.chad.library.adapter.base.BaseQuickAdapter;
import com.github.tvbox.osc.R;
import com.github.tvbox.osc.base.BaseActivity;
import com.github.tvbox.osc.bean.AppClassification;
import com.github.tvbox.osc.bean.App;
import com.github.tvbox.osc.bean.Msg;
import com.github.tvbox.osc.databinding.ClassifyBinding;
import com.github.tvbox.osc.ui.adapter.ClassifyListAdapter;
import com.github.tvbox.osc.util.ToolUtils;
import com.github.tvbox.osc.util.immersive.IImmersiveWindow;
import com.github.tvbox.osc.util.immersive.ImmersiveWindow;
import com.github.tvbox.osc.util.load.ILoadController;
import com.github.tvbox.osc.util.load.TVLoadController;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.lzy.okgo.OkGo;
import com.lzy.okgo.callback.StringCallback;
import com.lzy.okgo.model.Response;

import java.util.List;

/**
 * 分类页
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class ClassifyActivity extends BaseActivity {

    private final String tag = getClass().getSimpleName();

    private List<AppClassification> classifications;

    private ClassifyListAdapter classifyListAdapter;

    private AppClassification currentItem;

    private ILoadController classifyLoadController;

    private ILoadController appsLoadController;

    private ClassifyBinding binding;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ClassifyBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());
        binding.appListView.setLayoutManager(new LinearLayoutManager(this, LinearLayoutManager.VERTICAL, false));
        binding.appListView.addItemDecoration(new RecyclerView.ItemDecoration() {
            @Override
            public void getItemOffsets(@NonNull Rect outRect, int itemPosition, @NonNull RecyclerView parent) {
                super.getItemOffsets(outRect, itemPosition, parent);
                int margin = (int) (getResources().getDisplayMetrics().density * 10);
                if (itemPosition == 0) {
                    outRect.top = margin;
                }
                outRect.bottom = margin;
            }
        });
        classifyListAdapter = new ClassifyListAdapter();
        binding.appListView.setAdapter(classifyListAdapter);
        classifyListAdapter.setOnItemClickListener(new BaseQuickAdapter.OnItemClickListener() {
            @Override
            public void onItemClick(BaseQuickAdapter adapter, View view, int position) {
                Intent intent = new Intent(ClassifyActivity.this, AppDetailActivity.class);
                intent.putExtra("data", classifyListAdapter.getData().get(position));
                startActivity(intent);
            }
        });
        initAppLoad();
        initClassifyLoad();
        loadClassify();
    }

    @Override
    protected void onResume() {
        super.onResume();
        immersiveWindow();
    }

    private void immersiveWindow() {
        ImmersiveWindow immersiveWindow = new ImmersiveWindow(getWindow());
        immersiveWindow.updateWindow();
        immersiveWindow.setNavigation(false);
        immersiveWindow.setStatus(false);
    }

    private void loadClassify() {
        Runnable loadRunnable = new Runnable() {
            @Override
            public void run() {
                loadClassifyImpl();
            }
        };
        classifyLoadController.setLoadTask(loadRunnable);
        loadRunnable.run();
    }

    private void loadApps(View itemView, int id) {
        Runnable loadRunnable = new Runnable() {
            @Override
            public void run() {
                loadAppsImpl(itemView, id);
            }
        };
        appsLoadController.setLoadTask(loadRunnable);
        loadRunnable.run();
    }

    private void initClassifyLoad() {
        classifyLoadController = new TVLoadController((ViewGroup) binding.getRoot().getParent());
    }

    private void initAppLoad() {
        ViewGroup rootLayout = (ViewGroup) binding.appListView.getParent();
        appsLoadController = new TVLoadController(rootLayout);
    }

    private void initListLayout(List<AppClassification> list) {
        binding.classListLayout.removeAllViews();
        if (list == null || list.isEmpty()) {
            return;
        }
        for (AppClassification item : list) {
            View view = View.inflate(binding.classListLayout.getContext(), R.layout.home_label_rv_item_layout, null);
            ((TextView)view.findViewById(R.id.item_tv)).setText(item.name);
            View.OnFocusChangeListener onFocusChangeListener = new View.OnFocusChangeListener() {
                @Override
                public void onFocusChange(View view, boolean b) {
                    if (!b) {
                        return;
                    }
                    if (item == currentItem) {
                        return;
                    }
                    currentItem = item;
                    classifyListAdapter.setNewData(null);
                    loadApps(view, item.id);
                }
            };
            view.setOnFocusChangeListener(onFocusChangeListener);
            view.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    onFocusChangeListener.onFocusChange(view, true);
                }
            });
            binding.classListLayout.addView(view);
        }
        switchType(list.get(0).id);
    }

    private void switchType(int id) {
        if (classifications == null) {
            return;
        }
        int viewIndex = -1;
        for (int i = 0; i < classifications.size() ; i ++) {
            if (id == classifications.get(i).id) {
                viewIndex = i;
                break;
            }
        }
        if (viewIndex < 0) {
            return;
        }
        if (binding.classListLayout.getChildCount() > viewIndex) {
            binding.classListLayout.getChildAt(viewIndex).requestFocus();
        }
    }

    private void loadClassifyImpl() {
        OkGo.<String>post(ToolUtils.setApi("application_classify"))
                .execute(new StringCallback() {
                    @Override
                    public void onSuccess(Response<String> response) {
                        List<AppClassification> appClassifications = null;
                        try {
                            Msg<List<AppClassification>> msg = new Gson()
                                    .fromJson(response.body(), new TypeToken<Msg<List<AppClassification>>>(){}.getType());
                            appClassifications = msg.msg;
                        } catch (Throwable throwable) {
                            throwable.printStackTrace();
                        }
                        classifications = appClassifications;
                        initListLayout(appClassifications);
                        if (appClassifications != null && !appClassifications.isEmpty()) {
                            classifyLoadController.showSuccess();
                        } else {
                            classifyLoadController.showEmpty(null);
                        }
                    }

                    @Override
                    public void onError(Response<String> response) {
                        super.onError(response);
                        classifyLoadController.showError(null);
                    }

                });
        classifyLoadController.showLoading();
    }

    private void loadAppsImpl(View itemView, int id) {
        OkGo.<String>post(ToolUtils.setApi("application"))
                .params("classify_id", id)
                .execute(new StringCallback() {
                    @Override
                    public void onSuccess(Response<String> response) {
                        List<App> apps = null;
                        try {
                            Msg<List<App>> msg = new Gson()
                                    .fromJson(response.body(), new TypeToken<Msg<List<App>>>(){}.getType());
                            apps = msg.msg;
                        } catch (Throwable throwable) {
                            throwable.printStackTrace();
                        }
                        classifyListAdapter.setNewData(apps);
                        if (apps != null && !apps.isEmpty()) {
                            appsLoadController.showSuccess();
                        } else {
                            appsLoadController.showEmpty(itemView);
                        }
                    }

                    @Override
                    public void onError(Response<String> response) {
                        super.onError(response);
                        appsLoadController.showError(itemView);
                    }

                });
        appsLoadController.showLoading();
    }
}
