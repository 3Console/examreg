<template>
  <div class="tableContainer" :style="{width: widthTable}">
    <table>
      <thead>
      <tr @click="onSort">
        <slot/>
      </tr>
      </thead>
      <tbody>
      <slot name="first_row"/>
      <slot name="body" v-for="(row, index) in rows" :item="row" :index="index" :realIndex="(page - 1) * limit + index + 1"/>
      <template v-if="emptyData">
        <tr>
          <td :colspan="column" class="no_data_row">
            <img src="/images/no-data.svg">
            <span>{{ $t('common.empty_data') }}</span>
          </td>
        </tr>
      </template>
      <template v-else>
        <template v-for="row in emptyRow">
          <tr>
            <template v-for="col in column">
              <td></td>
            </template>
          </tr>
        </template>
      </template>
      <slot name="end_row"/>
      </tbody>
    </table>
    <template v-if="lastPage > 1 || visiblePagination">
      <pagination ref="pagination"
                  class="text-center"
                  :per-page="perPage"
                  :records="totalRecord"
                  :chunk="chunk"
                  @change-limit="onChangeLimit($event)"
                  @Pagination:page="onPageChange" :pageParent="page">
      </pagination>
    </template>
  </div>
</template>

<script>
  import Pagination from './Pagination';

  export default {
    components: {
      Pagination
    },
    props: {
      getData: {
        type: Function,
      },
      limit: {
        type: Number,
        default: 10
      },
      column: {
        type: Number,
        default: 0
      },
      chunk: {
        type: Number,
        default: 6
      },
      widthTable: {
        type: String,
        default: '100%'
      }
    },
    data() {
      return {
        visiblePagination: false,
        internalLimit: 0,
        maxPageWidth: 10,
        totalRecord: 0,
        lastPage: 0,
        page: 1,
        perPage: 10,
        fetching: false,
        rows: [],
        params: {},

        orderBy: null,
        sortedBy: null,
      };
    },
    computed: {
      emptyRow() {
        let emptyRowCount = Math.max(this.internalLimit - _.size(this.rows), 0);
        return Math.min(emptyRowCount, this.internalLimit);
      },
      emptyData () {
        if (this.fetching) {
          return false;
        }
        return window._.isEmpty(this.rows);
      },
    },
    watch: {
      limit(newValue) {
        this.internalLimit = newValue;
      }
    },
    methods: {
      onChangeLimit(limit) {
        this.visiblePagination = true;
        this.internalLimit = limit;
        this.refresh();
      },

      onPageChange(page) {
        this.page = page;
        this.fetch();
      },

      getTarget(target) {
        let node = target;
        while (node.parentNode.nodeName !== 'TR') {
          node = node.parentNode;
        }
        return node;
      },

      getSortOrder(target) {
        let sortOrder = target.dataset.sortOrder;
        switch (sortOrder) {
          case 'asc':
            sortOrder = '';
            break;
          case 'desc':
            sortOrder = 'asc';
            break;
          default:
            sortOrder = 'desc';
        }
        return sortOrder;
      },

      setSortOrders(target, sortOrder) {
        let iterator = target.parentNode.firstChild;
        while (iterator) {
          iterator.dataset.sortOrder = '';
          iterator = iterator.nextElementSibling;
        }
        target.dataset.sortOrder = sortOrder;
      },

      onSort(event) {
        const target = this.getTarget(event.target);
        const orderBy = target.dataset.sortField;
        if (!orderBy) {
          return
        }
        this.sortedBy = this.getSortOrder(target);
        this.orderBy = this.sortedBy ? orderBy : '';
        Object.assign(this.params, {sort: this.orderBy, sort_type: this.sortedBy});
        this.setSortOrders(target, this.sortedBy);
        this.fetch();
      },

      fetch() {
        const meta = {
          page: this.page,
          limit: this.internalLimit
        };

        this.fetching = true;
        this.getData(Object.assign(meta, this.params)).then((res) => {
          const data = res.data;
          if (!data) {
            return;
          }
          if (!data.data) {
            this.rows = data;
            this.page = parseInt(data.current_page) ? parseInt(data.current_page) : parseInt(res.current_page);
            this.totalRecord = parseInt(data.total) ? parseInt(data.total) : parseInt(res.total) ;
            this.lastPage = parseInt(data.last_page) ? parseInt(data.last_page) :  parseInt(res.last_page);
            this.perPage = parseInt(data.per_page) ? parseInt(data.per_page) : parseInt(res.per_page);
            this.$emit('DataTable:finish');
            return;
          }
          this.page = parseInt(data.current_page);
          this.totalRecord = parseInt(data.total);
          this.lastPage = parseInt(data.last_page);
          this.perPage = parseInt(data.per_page);
          this.rows = data.data;
          this.$emit('DataTable:finish');
        }).then((res) => {
          this.fetching = false;
        });
      },
      refresh() {
        this.page = 1;
        this.params = {};
        this.fetch();
      },

      filter(params) {
        this.page = 1;
        this.params = params;
        this.fetch();
      }
    },
    created() {
      this.internalLimit = this.limit;
      this.fetch();
      this.$on('DataTable:filter', (params) => {
        this.filter(params);
      });
    }
  };
</script>

<style lang="scss" scoped>
  @import "../../../sass/_variables";
  .no_data_row {
    text-align: center;
    padding: 30px;
    &:hover {
      background-color: #fff;
    }
    img {
      width: 50px;
    }
    span {
      font-size: 16px;
      vertical-align: sub;
      margin-left: 10px;
      color: #999999;
    }
  }

  table {
    width: 100%;
    background-color: $color_white;
    // border: $border_thin;
    overflow-y: hidden;
    thead {
      color: $color_grey;
      background-color: $color_white;
      border: none;
      overflow: hidden;
      th {
        position: relative;
        background-color: $color_white;
        line-height: 15px;
        font-size: $font_root;
        font-weight: 500;
        color: $color_grey;
        padding: 5px 15px;
        border-bottom: 1px solid $color_alto;
        height: 40px;
         &::after{
           font-family: "icomoon" !important;
           font-size: $font_root;
           margin-left: 5px;
           position: relative;
           top: 0px;
           width: 14px;
           height: 14px;
           display: inline-block;
           line-height: 14px;
           overflow: hidden;
           position: relative;
           top: 1px;
           content: "";
         }
        &[data-sort-order] {
          line-height: 15px;
          &::after{
            font-family: "icomoon" !important;
            font-size: $font_root;
            margin-left: 5px;
            position: relative;
            top: 0px;
            width: 14px;
            height: 14px;
            display: inline-block;
            line-height: 14px;
            overflow: hidden;
            position: relative;
            top: 1px;
            content: "";
          }
        }
        &[data-sort-order=asc] {
          color: $color_corn_pale;
          &::after{
            content: "\e955";
          }
        }
        &[data-sort-order=desc] {
          color: $color_corn_pale;
          &::after{
            content: "\e956";
          }
        }
      }

    }
    tbody {
      tr {
        vertical-align: top;
        overflow-y: hidden;
        transition-property: height;
        transition-duration: 0.3s, 0.3s;
        transition-timing-function: ease, ease-in;
        height: auto;
        background-color: $color_white;
        border-bottom: 1px solid $color_catskill_white;
        &:hover {
          background-color: $color_champagne;
        }
        &.active {
          height: 100px;
          max-height: 300px;
          background-color: $color_grey_select;
          transition-property: height;
          transition-duration: 0.3s, 0.3s;
          transition-timing-function: ease, ease-in;
          .glyphicon-menu-down {
            transition-duration: 0.5s;
            transform: rotate(180deg);
          }
        }
      }
      td {
        height: 40px;
        overflow: initial;
        line-height: 23px;
        font-size: $font_root;
        font-weight: 500;
        color: $color_mine_shaft;
        padding: 8px 15px 4px 15px;
        border-top: 1px solid $color_catskill_white;
      }
    }

  }
</style>
