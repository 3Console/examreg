<template>
  <div id="device-manager">
    <data-table :getData="getDevices" :widthTable="'100%'" :classHeader="'header-css'"
      @DataTable:finish="onDatatableFinished" ref="datatable" :limit="10" :column="4" class="datatable scroll">
      <th class="text-left">{{$t('user.device')}}</th>
      <th class="text-left">{{$t('user.recent_activity')}}</th>
      <th class="text-left">{{$t('account.ip_address')}}</th>
      <th class="text-right"></th>
      <template slot="body" slot-scope="props">
        <tr class="device-manager" :class="props.index % 2 === 0 ? 'odd' : 'active'">
          <td class="text-left">{{ props.item.kind | uppercaseFirst }}, {{props.item.operating_system}}, {{props.item.platform}}</td>
          <td class="text-left">{{ props.item.updated_at }}</td>
          <td class="text-left">{{ props.item.latest_ip_address }}</td>
          <td class="text-right">
            <button class="actions btn button_drop_history" @click="activeRow(props.item)">
              <i class="glyphicon" :class="props.item.isActiveRow ? 'icon-arrow2' : 'icon-arrow1'"></i>
            </button>
            <button class="actions btn button_close_history" @click.prevent="confirmRemoveDevice(props.item)">
              <i class="icon-close"></i>
            </button>
          </td>
        </tr>
        <template v-if="props.item.isActiveRow">
          <tr
            class="device-manager text-left"
            :class="{ odd: ((props.index + 1) % 2 === 0) }"
            v-for="connection in props.item.user_connection_histories">
            <td></td>
            <td>{{ connection.created_at | timestampToDate }}</td>
            <td>{{ connection.ip_address }}</td>
            <td></td>
          </tr>
        </template>
      </template>
    </data-table>
  </div>
</template>

<script>
  import rf from '../../lib/RequestFactory';

  export default {
    props: {
      userId: { type: [Number, String] }
    },
    data() {
      return {
      }
    },
    watch: {
      userId(newValue) {
        this.$refs.datatable.refresh();
      }
    },
    methods: {
      onDatatableFinished() {
        if (!this.$refs.datatable || !this.$refs.datatable.rows) {
          return;
        }
        window._.each(this.$refs.datatable.rows, item => {
          item.isActiveRow = false;
        });
      },
      activeRow(item) {
        item.isActiveRow = !item.isActiveRow;
        this.$forceUpdate();
      },
      getDevices (params) {
        if (window._.isEmpty(`${this.userId}`)) {
          return new Promise(() => {});
        }
        this.isActiveRow = -1;
        params.userId = this.userId;
        return rf.getRequest('UserRequest').getDeviceRegister(params);
      },
      refresh() {
        this.$refs.datatable.refresh();
      },
      confirmRemoveDevice (deviceId) {
        window.ConfirmationModal.show({
          type: 'primary',
          title: '',
          content: this.$t('account.modal.confirm_trusted_device'),
          btnCancelLabel: window.i18n.t('common.action.no'),
          btnConfirmLabel: window.i18n.t('common.action.yes'),
          onConfirm: () => {
            this.removeDevice(deviceId);
          }
        });
      },
      removeDevice (item) {
        const params = {
          userId: item.user_id,
          deviceId: item.id
        };
        rf.getRequest('UserRequest').deleteDevice(params).then((res) => {
          this.$toastr('success', this.$t('address.deleted_success'));
          this.refresh();
        });
      },
    },
  }
</script>

<style lang="scss" scoped>
  @import "../../../../sass/_variables";

  .device-manager {
    &.active {
      height: auto; 
      max-height: auto;
      background-color: transparent; 
    }
  }

  #device-manager {
    max-height: 441px;
    overflow-y: auto; 
    .datatable {
      .width-title {
        min-width: 160px;
        max-width: 160px;
      }
      button {
        background: transparent;
        box-shadow: none;
        border: 0;
        width: 27px;
        height: 27px;
        padding: 4px;
        float: right;
        &:hover {
          background-color: transparent;
          color: $color_yellow_text;
        }
      }
    }
  }
  .button_close_history {
    margin-left: 10px;
  }
  .icon-arrow2 {
    color: #55d184;
  }
</style>
