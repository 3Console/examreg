<template>
  <div>
    <div class="VuePagination clearfix">
      <div class="group_number_items">
        <span class="txt_nb_itme">Number items</span>
        <select class="list_number_items" v-model="limit">
          <option v-for="item in optionPages" :value="item.value">{{ item.name }}</option>
        </select>
      </div>
      <ul v-show="totalPages > 1"
          class="pagination VuePagination__pagination">

        <!--<li class="VuePagination__pagination-item page-item VuePagination__pagination-item-prev-chunk "-->
            <!--:class="{disabled : !allowedChunk(-1)}">-->
          <!--<a class="page-link" href="javascript:void(0);"-->
             <!--@click="setChunk(-1)">&lt;&lt;</a>-->
        <!--</li>-->


        <li class="VuePagination__pagination-item page-item VuePagination__pagination-item-prev-page page-prev"
            :class="{disabled : !allowedPage(page - 1)}">
          <a class="page-link " href="javascript:void(0);"
             @click="prev()">{{ $t('common.pagination.pre') }}</a>
        </li>

        <template v-for="item in pages">
          <li class="VuePagination__pagination-item page-item "
              :class="{active: parseInt(page) === parseInt(item)}">
            <a class="page-link" role="button"
               @click="setPage(item)">{{item}}</a>
          </li>
        </template>

        <li class="VuePagination__pagination-item page-item VuePagination__pagination-item-next-page page-next"
            :class="{disabled : !allowedPage(page + 1)}">
          <a class="page-link" href="javascript:void(0);"
             @click="next()">{{ $t('common.pagination.next') }}</a>
        </li>

        <!--<li class="VuePagination__pagination-item page-item VuePagination__pagination-item-next-chunk "-->
            <!--:class="{disabled : !allowedChunk(1)}">-->
          <!--<a class="page-link" href="javascript:void(0);"-->
             <!--@click="setChunk(1)">&gt;&gt;</a>-->
        <!--</li>-->
      </ul>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      pageParent: {
        type: Number,
        default: 1,
      },
      records: {
        type: Number,
        required: true
      },
      chunk: {
        type: Number,
        required: false,
        default: 6
      },
      perPage: {
        type: Number,
        required: true,
      },
    },
    data: function () {
      return {
        page: this.pageParent,
        limit: this.perPage,

        optionPages: [],
      }
    },
    watch: {
      records() {
        if (this.page > this.totalPages) {
          this.page = 1;
        }
      },
      pageParent() {
        this.page = this.pageParent;
      },
      limit(newValue) {
        this.$emit('change-limit', parseFloat(newValue));
      }
    },
    computed: {
      pages: function () {
        if (!this.records)
          return []

        return range(this.paginationStart, this.chunk, this.totalPages)
      },
      totalPages: function () {
        return this.records ? Math.ceil(this.records / this.perPage) : 1
      },
      totalChunks: function () {
        return Math.ceil(this.totalPages / this.chunk)
      },
      currentChunk: function () {
        return Math.ceil(this.page / this.chunk)
      },
      paginationStart: function () {
        return ((this.currentChunk - 1) * this.chunk) + 1
      },
      pagesInCurrentChunk: function () {

        return this.paginationStart + this.chunk <= this.totalPages ? this.chunk : this.totalPages - this.paginationStart + 1

      },
    },
    methods: {
      setPage: function (page) {
        if (this.allowedPage(page)) {
          this.paginate(page)
        }
      },
      paginate (page) {
        this.page = page
        this.$emit('Pagination:page', page)
      },
      next: function () {
        return this.setPage(this.page + 1)
      },
      prev: function () {
        return this.setPage(this.page - 1)
      },
      setChunk: function (direction) {
        this.setPage((((this.currentChunk - 1) + direction) * this.chunk) + 1)
      },
      allowedPage: function (page) {
        return page >= 1 && page <= this.totalPages
      },
      allowedChunk: function (direction) {
        return (parseInt(direction) === 1 && this.currentChunk < this.totalChunks)
          || (parseInt(direction) === -1 && this.currentChunk > 1)
      },
      allowedPageClass: function (direction) {
        return this.allowedPage(direction) ? '' : 'disabled'
      },
      allowedChunkClass: function (direction) {
        return this.allowedChunk(direction) ? '' : 'disabled'
      },
      activeClass: function (page) {
        return parseInt(this.page) === parseInt(page) ? 'active' : ''
      },

      getOptionPages() {
        const limit = window._.cloneDeep(this.perPage);
        this.optionPages = [
          { value: limit, name: limit},
          { value: limit * 2, name: limit * 2},
          { value: limit * 5, name: limit * 5},
          { value: limit * 10, name: limit * 10},
        ];
      }
    },
    created() {
      this.getOptionPages();
    }
  }

  function range (start, chunk, total) {
    if( start + chunk > total) {
      start = Math.max(total - chunk + 1, 1);
    }
    var end = chunk > total ? total : chunk;
    return Array.apply(0, Array(end))
      .map(function (element, index) {
        return index + start
      })
  }
</script>

<style lang="scss" scoped>
  @import "../../../../sass/_variables.scss";

  .VuePagination {
    margin: 15px 0px;

    .group_number_items {
      display: inline-block;
      float: left;
      line-height: 20px;
      color: $color_dark_gray;
      font-size: $font_small;
      font-weight: 500;

      .txt_nb_itme {
        display: inline-block;
        float: left;
        margin-right: 8px;
        line-height: 20px;
        padding: 5px 0px; 
      }
      .list_number_items {
        box-shadow: inset 0 0px 0px rgba(0, 0, 0, 0.0);
        border-radius: 0px; 
        height: 30px;
        border: 1px solid $color_dark_gray;
        background-color: transparent;
        color: $color_dark_gray;
        font-size: $font_small;
        line-height: 20px;
        padding: 4px;
      }
    }
    .VuePagination__pagination {
      float: right;
      margin: 0px;

      li {
        a {
          background-color: transparent;
          border: 0px;
          height: 30px;
          min-width: 30px;
          line-height: 20px;
          text-align: center;
          padding: 5px;
          color: $color_dark_gray;
          font-size: $font_small;
          font-weight: 500;
          border-radius: 0px;
          cursor: pointer;
          &:hover {
            color: $color_white;
            background: #0a3e69;
          }          
          &:focus {
            outline: -webkit-focus-ring-color auto 0px;
            box-shadow: 0 0 0 0 rgba(52, 144, 220, 0);
          }
        }
        &:focus {
          outline: -webkit-focus-ring-color auto 0px;
          box-shadow: 0 0 0 0 rgba(52, 144, 220, 0);
        }
        &.disabled {
          cursor: not-allowed;
        }
        &.active {
          a {
            color: $color_white;
            background: #0a3e69;
          }
        }
        &.page-next {
          a {
            width: 55px !important;
            min-width: 53px !important;
          }
        }
        &.page-prev {
          a {
            width: 70px !important;
            min-width: 70px !important;
          }
        }
      }
    }
  }

</style>
