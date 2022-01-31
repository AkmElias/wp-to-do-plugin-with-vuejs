<template>
  <el-dialog title="Create Custom To Do Format 🔥"  :visible="showForm" center  :before-close="handleClose">

    <el-form ref="form" :rules="rules" :model="form" :label-width="formLabelWidth">
      <el-form-item label="Title" :label-width="formLabelWidth" prop="title">
        <el-input v-model="form.title" autocomplete="off" placeholder="Eg: Daily to do's, Long term to do's.."></el-input>
      </el-form-item>
      <el-form-item label="Limit" :label-width="formLabelWidth">
        <el-select v-model="form.list_limit" placeholder="Please choose the limit.">
          <el-option label="Minimum" value="10"></el-option>
          <el-option label="Medium" value="15"></el-option>
          <el-option label="Maximum" value="20"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Theme" :label-width="formLabelWidth">
        <el-select v-model="form.theme" placeholder="Choose your favourite theme.">
          <el-option label="Default" value="default"></el-option>
          <el-option label="Light" value="light"></el-option>
          <el-option label="Dark" value="dark"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Show Completed List.">
        <el-switch v-model="form.show_completed"></el-switch>
      </el-form-item>
    </el-form>
    <span slot="footer" style="text-align: center">
    <el-button @click="hide">Cancel</el-button>
    <el-button type="primary" @click="onSubmit('form')">{{submitButtonText}}</el-button>
  </span>
  </el-dialog>
</template>

<script>
import { ElMessageBox } from 'element-ui';

export default {
  name: "FormModal",
  props: {
    showFormModal: Boolean,
    editableTodo: Object,
  },
  emits: ['toggleModal', 'loadCustomToDos'],
  data(){
    return {
      formLabelWidth: '150px',
      showForm: this.showFormModal,
      submitButtonText: "Create",
      form: {
        title: '',
        list_limit: null,
        show_completed: true,
        theme: '',
      },
      rules: {
        title: [
          {required: true, message: 'Please input Title', trigger: 'blur'},
          {min: 3, max: 20, message: 'Length should be 3 to 20', trigger: 'blur'}
        ],
      }
    }
},
  mounted() {
    if(this.editableTodo.id){
       this.submitButtonText = "Update";
       this.form.title = this.editableTodo.title;
       this.form.list_limit = this.editableTodo.list_limit;
       this.form.show_completed = this.editableTodo.show_completed == 'true' ? true : false;
       this.form.theme = this.editableTodo.theme;
    }
  },
  watch: {
    showForm(newVal){
      if(newVal === this.showForm){
        return;
      } else {
        this.showForm = newVal;

      }
    }
  },
  methods: {
    handleClose(){
      if(confirm('Are you sure to close this dialog?')){
        this.$emit('toggleModal', false)
      }
    },

    hide()
    {
      this.$emit('toggleModal', false);
    },

    onSubmit(form)
    {
     this.$refs[form].validate((valid) => {
        if (valid) {
          if(this.editableTodo.id){
            this.updateCustomTodo(form);
          } else{
            this.createCustomTodo(form);
            console.log('submit!: ', this.form);
          }
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },

    createCustomTodo(form)
    {
      alert('submit!');
      const _this = this;
      jQuery.ajax({
        method: 'POST',
        url: ajax_obj.ajaxurl,
        dataType: "json",
        data: {
          action: "to_do_admin_ajax",
          route: "add_custom_todo",
          title: this.form.title,
          list_limit: this.form.list_limit,
          show_completed: this.form.show_completed,
          theme: this.form.theme,
          nonce: ajax_obj.nonce,
        },
        success(res) {
          if(res) {
            _this.$emit('toggleModal', false);
            _this.$emit('loadCustomToDos');
          }
        },
        error({ responseJSON: { data } }, _, err) {
          console.log("error!!1");
          // state.error = data;
        },
      })
    },

    updateCustomTodo(form)
    {
      alert('update!');
      const _this = this;
      jQuery.ajax({
        method: 'POST',
        url: ajax_obj.ajaxurl,
        dataType: "json",
        data: {
          action: "to_do_admin_ajax",
          route: "update_custom_todo",
          id: _this.editableTodo.id,
          title: this.form.title,
          list_limit: this.form.list_limit,
          show_completed: this.form.show_completed,
          theme: this.form.theme,
          nonce: ajax_obj.nonce,
        },
        success(res) {
          if(res) {
            _this.$emit('toggleModal', false);
            _this.$emit('loadCustomToDos');
          }
        },
        error({ responseJSON: { data } }, _, err) {
          console.log("error!!1");
          // state.error = data;
        },
      })
    }
  },

}
</script>

<style scoped>

</style>