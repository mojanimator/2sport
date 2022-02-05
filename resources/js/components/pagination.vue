<template>

    <ul class="justify-content-center pagination">

        <!--first/previouse-->

        <template v-if=" paginator.current_page==1">
            <li class="page-item   ">
                <span class="page-link rounded-start  " aria-hidden="true">&laquo;</span>
            </li>
            <li class="page-item  ">
                <span class="page-link  " aria-hidden="true">&lsaquo;</span>
            </li>
        </template>

        <template v-if=" paginator.current_page > 1">
            <li class="page-item">
                <a class="page-link rounded-start" aria-label="First" @click="updateData( 1 )">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <li class="page-item">
                <a class="page-link" aria-label="Previous"
                   @click="updateData( paginator.current_page-1 )">
                    <span aria-hidden="true">&lsaquo;</span>
                </a>
            </li>
        </template>

        <!--links-->

        <li v-for="i in this.range(this.from,this.to)" class="page-item "
            :class="{' active' :  paginator.current_page==i }">
            <a class="page-link" @click="updateData( i )">
                {{farsiDigit(i) }}
            </a>
        </li>

        <!-- next/last -->

        <template v-if="paginator.current_page < paginator.last_page">
            <li class="page-item">
                <a class="page-link" aria-label="Next"
                   @click="updateData(paginator.current_page+1 )">
                    <span aria-hidden="true">&rsaquo;</span>
                </a>
            </li>

            <li class="page-item">
                <a class="page-link rounded-end" aria-label="Last"
                   @click="updateData(paginator.last_page)">
                    <span aria-hidden="true">{{farsiDigit(paginator.last_page)}}</span>
                </a>
            </li>
        </template>
        <!--disable next/last-->
        <template v-if="paginator.current_page >= paginator.last_page">
            <li class="page-item disabled">
                <a class="page-link" aria-label="Next">
                    <span aria-hidden="true">&rsaquo;</span>
                </a>
            </li>

            <li class="page-item disabled">
                <a class="page-link rounded-end " aria-label="Last">
                    <span aria-hidden="true">{{farsiDigit(paginator.last_page)}}</span>
                </a>
            </li>
        </template>

    </ul>

</template>

<script>


    export default {
        components: {},
        data() {
            return {
                paginator: {},
                interval: 3,
                from: 0,
                to: -1,
            }
        },
        props: [],
//        inject: ['paginator'],
        watch: {
//            paginator(val, old) {
//
//                this.updatePaginator(val);
//            }
        },
        mounted() {


        },
        created() {

        },

        methods: {
            farsiDigit(str) {
                return str;
                if (!str) return;
                str = str.toString();
                let eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                let per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                for (let i in eng) {
//                    str = str.replaceAll(eng[i], per[i]);
                    let re = new RegExp(eng[i], "g");
                    str = str.replace(re, per[i]);
                }
                return str;

            },
            updateData(page) {

                this.$parent.getData(page);
            },
            updatePaginator(paginator) {
                this.paginator = paginator;

                this.from = this.paginator.current_page - this.interval;
                if (this.from < 1)
                    this.from = 1;
                this.to = this.paginator.current_page + this.interval;
                if (this.to > this.paginator.last_page)
                    this.to = this.paginator.last_page;

            },
            setEvents() {

            },
            range(from, to) {
                let array = [],
                    j = 0;
                for (let i = from; i <= to; i++) {
                    array[j] = i;
                    j++;
                }
                return array;
            }
        }
    }

</script>

<style lang="scss">


    $primary: rgba(34, 32, 65, 1) !default;
    .pagination {

        .page-item {
            color: $primary;
            cursor: pointer;

        }
        .page-item.active .page-link {
            background-color: $primary;
        }
    }
</style>