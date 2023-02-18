<template>
    <div class="   shadow-lg position-relative mt-4">
        <div v-show="loading" class="  position-fixed start-0 top-50  end-0   text-center"
             style="  z-index: 10">
            <div v-for="i in  [1,1,1,1,1]"
                 class="spinner-grow mx-1   spinner-grow-sm text-danger "
                 role="status">
                <span class="visually-hidden"> </span>
            </div>

        </div>
        <div class=" card ">
            <div class=" card-header bg-primary text-white  ">
                <span class="d-inline-block  ">{{admin ? 'لیست بازاریاب ها' : 'اطلاعات بازاریابی'}}</span>

                <span v-if="admin" class="small  ms-1 ms-sm-5">
مجموع: {{sum}}  تومان
                </span>
                <span v-if="admin" class="d-inline-block btn btn-secondary ms-1 ms-sm-5 "
                      @click="  showDialog('confirm','از تسویه کاربران انتخابی اطمینان دارید؟', tasvie ,null)">تسویه
                </span>
            </div>
            <div class="card-body   px-0 p-sm-1  ">
                <div class="table-responsive-sm position-absolute  start-0 end-0 overflow-x-scroll">
                    <table class="table  table-sm table-primary   table-striped table-light   "
                    >
                        <thead>
                        <tr>
                            <th class="small col-1 text-center" scope="col" style="white-space: nowrap;">#</th>
                            <th class="col  text-center">نام</th>
                            <th class="col  text-center">سطح۱</th>
                            <th class=" col   text-center">سطح۲</th>
                            <th class=" col   text-center">سطح۳</th>
                            <th class=" col   text-center">سطح۴</th>
                            <th class=" col   text-center">سطح۵</th>
                            <th class="    text-center hoverable-dark" @click="sort()">بستانکار(ت)</th>
                            <th v-if="admin" class=" col-1   text-center">انتخاب</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="d,idx in refs" class=" " :key="d.id">
                            <th scope="row" class=" p-0 p-sm-1 text-center">{{d.id}}
                            </th>
                            <td class=" small p-0 p-sm-1 ">
                                {{d.name ? d.name + ' ' + d.family : d.username}}
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle">
                                <div class="small text-success">
                                    {{'(' + d.levels[1]['paid']['count'] + ') ' + d.levels[1]['paid']['sum']}}
                                </div>
                                <div class="small text-danger">
                                    {{'(' + d.levels[1]['unpaid']['count'] + ') ' + d.levels[1]['unpaid']['sum']}}
                                </div>
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle">
                                <div class="small text-success">
                                    {{'(' + d.levels[2]['paid']['count'] + ') ' + d.levels[2]['paid']['sum']}}
                                </div>
                                <div class="small text-danger">
                                    {{'(' + d.levels[2]['unpaid']['count'] + ') ' + d.levels[2]['unpaid']['sum']}}
                                </div>
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle">
                                <div class="small text-success">
                                    {{'(' + d.levels[3]['paid']['count'] + ') ' + d.levels[3]['paid']['sum']}}
                                </div>
                                <div class="small text-danger">
                                    {{'(' + d.levels[3]['unpaid']['count'] + ') ' + d.levels[3]['unpaid']['sum']}}
                                </div>
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle">
                                <div class="small text-success">
                                    {{'(' + d.levels[4]['paid']['count'] + ') ' + d.levels[4]['paid']['sum']}}
                                </div>
                                <div class="small text-danger">
                                    {{'(' + d.levels[4]['unpaid']['count'] + ') ' + d.levels[4]['unpaid']['sum']}}
                                </div>
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle">
                                <div class="small text-success">
                                    {{'(' + d.levels[5]['paid']['count'] + ') ' + d.levels[5]['paid']['sum']}}
                                </div>
                                <div class="small text-danger">
                                    {{'(' + d.levels[5]['unpaid']['count'] + ') ' + d.levels[5]['unpaid']['sum']}}
                                </div>
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle">
                                {{"(" + d.bestan.count + ") " + d.bestan.sum}}
                            </td>

                            <td v-if="admin" class="small p-0 p-sm-1 text-center align-middle"
                                style="white-space: nowrap;"
                            >

                                <input type="checkbox" v-model="d.selected" @change="calcSum('selected')"/>
                            </td>
                        </tr>

                        </tbody>
                    </table>

                    <h5 v-if="refs.length==0" class="text-center">موردی وجود ندارد</h5>

                    <div v-if="!admin" class="text-center w-100">
                        لینک بازاریابی:
                    </div>
                    <span v-if="!admin" class="input-group  ">
                    <button class="btn btn-secondary" @click="copy()">کپی</button>
                        <input id="copy" type="text " class="form-control  " :value="refLink">
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['admin', 'searchLink', 'tasvieLink', 'refLink'],
        data() {
            return {

                loading: false,
                params: {},
                refs: [],
                link: null,
                sum: 0,
                toggleSort: false,
            }
        },
        created() {
            this.getData();
        },
        mounted() {
        },
        methods: {
            showDialog(type, message, click, params) {
                window.showDialog(type, message, onclick = () => click, params);
            },
            copy() {
                var copyTextarea = document.querySelector('#copy');
                copyTextarea.focus();
                copyTextarea.select();
                try {
                    var successful = document.execCommand('copy');
                    if (successful) {
                        window.showToast('success', 'با موفقیت کپی شد')
                    } else {
                        window.showToast('danger', 'قادر به کپی نیست')
                    }
                } catch (err) {
                    window.showToast('danger', err)
                }
            },
            sort() {
                this.toggleSort = !this.toggleSort;
                this.refs.sort(function (a, b) {

                    return a.bestan.sum - b.bestan.sum;
                });
                if (this.toggleSort)
                    this.refs.reverse();
            },
            calcSum(type) {
                this.sum = 0;
                if (type === 'selected')
                    for (let id in this.refs) {
                        if (this.refs[id].selected)
                            this.sum += this.refs[id].bestan.sum;
                    }

            },
            tasvie() {
                this.link = this.tasvieLink;
                let data = [];
                for (let id in this.refs) {
                    if (this.refs[id].selected)
                        data.push(this.refs[id].id);
                }
                if (data.length === 0) {
                    window.showToast('danger', 'موردی انتخاب نشده است');
                    return;
                }
                this.sendData({ids: data});
            },
            sendData(data) {
                this.loading = true;

                axios.post(this.link, data, {})

                    .then((response) => {
//                        console.log(response);
                            this.loading = false;

                            if (response.status === 200) {

                                this.setting = response.data;
                                this.param = {};
                                window.showToast('success', 'با موفقیت اجام شد!');
                                this.getData();
                            }


                        }
                    ).catch((error) => {
                    this.loading = false;

                    let errors = '';
                    if (error.response && error.response.status === 422)
                        for (let idx in error.response.data.errors)
                            errors += error.response.data.errors[idx] + '<br>';
                    else {
                        errors = error;
                    }
                    window.showToast('danger', errors);

                });
            },
            getData() {
                this.loading = true;

                axios.get(this.searchLink, {params: this.params}, {})

                    .then((response) => {
//                            console.log(response);
                            this.loading = false;

                            if (response.status === 200) {
                                this.refs = response.data;
                                this.toggleSort = false;
                                this.sort();
                            }
//                            window.location.reload();


                        }
                    ).catch((error) => {
                    this.loading = false;

                    let errors = '';
                    if (error.response && error.response.status === 422)
                        for (let idx in error.response.data.errors)
                            errors += error.response.data.errors[idx] + '<br>';
                    else {
                        errors = error;
                    }
                    window.showToast('danger', errors);

                });
            },
        },
    }
</script>
