<template>
    <div>
        <el-container>
            <el-header class="header" style="height: fit-content !important;">
              <el-button @click="show" >Add custom ToDo</el-button>
            </el-header>
            <el-main>
              <el-alert v-if="successAlert" :title="successMessage" type="success" effect="dark" style="margin-bottom: 5px" @close="hideAlert"></el-alert>
                <form-modal v-if="showFormModal" @toggleModal="toggleModal" @loadCustomToDos="loadCustomToDos" :showFormModal="showFormModal" :editableTodo="editableTodo"/>
              <el-table :data="tableData" class="custom-table">

                <el-table-column prop="id" label="ID"/>
<!--                <el-table-column prop="shortcode" label="Shortcode" width="250"></el-table-column>-->
                <el-table-column prop="shortcode" label="Shortcode" width="180" #default="scope">
                  <el-tooltip
                      class="box-item"
                      effect="dark"
                      :content="tooltipContent"
                      placement="top"
                  >
                    <input
                        :id="generateId(scope.$index)"
                        class="shortcode"
                        @click="copyUrl(scope.$index)"
                        :value="'[wp-todo id=\''+ tableData[scope.$index].id +'\']'"
                        readonly
                    />
                  </el-tooltip>
                </el-table-column>
                <el-table-column prop="title" label="Title" />
                <el-table-column prop="list_limit" label="List_limit"  />
                <el-table-column prop="show_completed" label="Show_completed" width="150"/>
                <el-table-column prop="theme" label="Theme"  />
                <el-table-column prop="operations" label="Operations" width="180">
                  <template slot-scope="scope">
                    <el-button size="small" @click="handleEdit(tableData[scope.$index])"
                    >Edit</el-button
                    >
                    <el-button
                        size="small"
                        type="danger"
                        @click="handleDelete(tableData[scope.$index].id)"
                    >Delete</el-button
                    >
                  </template>
                </el-table-column>
              </el-table>
            </el-main>
        </el-container>
        <!-- example content end -->
    </div>
</template>

<script>
import FormModal from "../Components/FormModal";

export default
{
  name: 'Dashboard',
  components: {FormModal},
  data()
  {
      return {
          vueJs: 'https://vuejs.org/',
          showModal: false,
          tableData: [],
          shortcodeRef: null,
          tooltipContent: "Click to copy Shortcode!",
          showFormModal: false,
          editableTodo: {},
          successAlert: false,
          successMessage: "",
      }
  },
  mounted()
  {
      this.loadCustomToDos();
      // console.log('data', this.tableData);
  },

  methods:
  {
      loadCustomToDos(message){
        let _this = this;

        jQuery.ajax({
          method: 'POST',
          url: ajax_obj.ajaxurl,
          dataType: "json",
          data: {
            action: "to_do_admin_ajax",
            route: "get_all_custom_todo",
            nonce: ajax_obj.nonce,
          },
          success(res) {
            if (res.success) {
              console.log("success!!1", res.data);
              _this.tableData = res.data;
              _this.successAlert = true;
              _this.successMessage = message ? message : "To Do Board Loaded";
            } else {
              // state.error = { nonce: req.message };
            }
          },
          error({ responseJSON: { data } }, _, err) {
             console.log("error!!1");
            // state.error = data;
          },
      })
    },

      generateId(index) {
      return 'shortcode_' + index;
    },

      show(){
        this.showFormModal = true;
        this.editableTodo = {};
        console.log('....called', this.showFormModal);
      },

      hideAlert(){this.successAlert = false},

      toggleModal(newVal){
        console.log('...trigered1')
        this.showFormModal = newVal;
        console.log("dashboard..", this.showFormModal);
      },

      copyUrl(index)
      {
        let copyText = document.getElementById("shortcode_" + index);
        copyText.select();
        document.execCommand("copy");

        this.tooltipContent = "Shortcode copied!!";

        setTimeout(() => this.tooltipContent = "Click to copy Shortcode!", 2000);
      },

      handleEdit(editableTodo)
      {
          this.show();
          this.editableTodo = editableTodo;
      },

      handleDelete(id)
      {
          console.log('delete call id: ',id );
          const _this = this;
          jQuery.ajax({
            method: 'POST',
            url: ajax_obj.ajaxurl,
            dataType: "json",
            data: {
              action: "to_do_admin_ajax",
              route: "delete_custom_todo",
              id: id,
              nonce: ajax_obj.nonce,
            },
            success: function(res){
              if(res){
                console.log(res);
                _this.loadCustomToDos("To Do Board Deleted!");

              }
            },
            error: function (response){
              if(typeof response.responseText === "string"){
                let message = response.responseText.split('"data":')[1].split('}')[0];
                alert(message);
              }
              // console.log(jQuery.parseJSON(response.responseText));
            }
          })
      },
    },
}
</script>

<style scoped>

.header {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: flex-start;
  margin: 10px 0 10px 15px;
  padding: 5px;
}
.custom-table{
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.shortcode{
  width: 90%;
  outline: 0;
  border-width: 0;
  padding: 5px 6px 7px 8px;
  border-radius: 2px !important;
  cursor: pointer;
  font-weight: 400;
  font-size: 13px;
  text-align: center;
}

.shortcode:hover{
  outline: none;
  background-color: #ffa92c;
  color:#FFF;
  box-shadow: none;
}

</style>

