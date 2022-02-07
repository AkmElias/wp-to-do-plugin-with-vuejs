import Vue from "./elements";
import Router from "vue-router";
Vue.use(Router);

import { applyFilters, addFilter, addAction, doAction } from "@wordpress/hooks";

export default class toDo {
  constructor() {
    this.applyFilters = applyFilters;
    this.addFilter = addFilter;
    this.addAction = addAction;
    this.doAction = doAction;
    this.Vue = Vue;
    this.Router = Router;
  }

  //   $publicAssets(path) {
  //     return window.toDoAdmin.assets_url + path;
  //   }

  //   $get(options) {
  //     return window.jQuery.get(window.toDoAdmin.ajaxurl, options);
  //   }

  //   $adminGet(options) {
  //     options.action = "to_do_admin_ajax";
  //     return window.jQuery.get(window.toDoAdmin.ajaxurl, options);
  //   }

  //   $post(options) {
  //     return window.jQuery.post(window.toDoAdmin.ajaxurl, options);
  //   }

  //   $adminPost(options) {
  //     options.action = "to_do_admin_ajax";
  //     return window.jQuery.post(window.toDoAdmin.ajaxurl, options);
  //   }

  //   $getJSON(options) {
  //     return window.jQuery.getJSON(window.toDoAdmin.ajaxurl, options);
  //   }
}
