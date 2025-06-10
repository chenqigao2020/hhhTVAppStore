package com.github.tvbox.osc.bean;

import com.google.gson.annotations.SerializedName;
import java.io.Serializable;

/**
 * 此版本由《红火火工作室》开发
 * 二开、源码请联系QQ：1282797911 闲鱼：红火火工作室
 * **/
public class App implements Serializable {

    @SerializedName("name")
    public String name;

    @SerializedName("icon")
    public String icon;

    @SerializedName("size")
    public String size;

    @SerializedName("only")
    public String only;

    @SerializedName("details")
    public String details;

    @SerializedName("version")
    public String version;

    @SerializedName("download")
    public String download;

}
