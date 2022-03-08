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
        <div class="  ">
            <div class="card card-header bg-primary text-white">
                کد های تخفیف
            </div>
            <div class="card-body   px-0 p-sm-1  ">
                <div class="table-responsive-sm  position-absolute start-0 end-0 overflow-x-scroll">
                    <table class="table  table-sm table-primary   table-striped table-light   "
                    >
                        <thead>
                        <tr>
                            <th class="small   text-center" scope="col" style="white-space: nowrap;">#</th>
                            <th class="   text-center px-5">کد تخفیف </th>
                            <th class="  text-center">درصد تخفیف</th>
                            <th class="    text-center">حد ‌تخفیف(ت)</th>
                            <th v-if="admin" class="    text-center">مخصوص کاربر</th>
                            <th class="    text-center">انقضا</th>
                            <th v-if="admin" class="    text-center">تعداد استفاده</th>
                            <th v-if="admin" class="     text-center">عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="d,idx in coupons" class=" " :key="d.id">
                            <th scope="row" class=" p-0 p-sm-1 text-center">{{d.id}}</th>

                            <td class=" small p-0 p-sm-1 text-center " style="white-space: nowrap;">{{d.code}}</td>
                            <td class="small p-0 p-sm-1 text-center  ">{{d.discount_percent}}  </td>
                            <td class=" small p-0 p-sm-1 text-center">{{d.limit_price ? d.limit_price : '_'}} </td>
                            <td v-if="admin && d.user_id" class=" small p-0 p-sm-1 text-center"><a
                                    :href="'/panel/user/edit/'+d.user_id">{{d.user_id}}</a></td>
                            <td v-if="admin && !d.user_id" class=" small p-0 p-sm-1 text-center"> _    </td>

                            <td class=" small p-0 p-sm-1 text-center">
                                {{d.expires_at ? new Date(d.expires_at).toLocaleDateString('fa-IR') : '_'}}
                            </td>
                            <td v-if="admin" class=" small p-0 p-sm-1 text-center">{{d.used_times}} </td>

                            <td v-if="admin" class="small p-0 p-sm-1 text-center align-middle"
                                style="white-space: nowrap;">
                                <div class="btn-group">

                                    <button title="حذف ردیف" class="btn btn-sm bg-danger  p-1 hoverable-primary  "
                                            @click="link=removeLink;param.id=d.id;  showDialog('confirm','از حذف اطمینان دارید؟', sendData ,{id:d.id})">
                                        <i class="fa   fa-trash  text-white   mx-2 "
                                           aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="admin" class=" ">
                            <th scope="row" class=" p-0 p-sm-1 text-center  ">

                                <button title="ساخت ردیف"
                                        class="btn  btn-lg bg-success  my-4  p-1 hoverable-primary  "
                                        @click=" link=createLink; showDialog('confirm','از افزودن اطمینان دارید؟', sendData ,param)">
                                    <i class="fa   fa-plus-circle  text-white   mx-2 "
                                       aria-hidden="true"></i>
                                </button>


                            </th>
                            <td class=" small p-0 p-sm-1 align-middle position-relative">
                                <input type="text" class="form-control px-1  "
                                       placeholder="کد" v-model="param.code">
                                <button title="کد اتوماتیک"
                                        class="position-absolute top-0 end-0 bottom-0 p-0  btn  btn-sm bg-secondary  my-4  p-1 hoverable-primary  "
                                        @click=" link=createLink; sendData({'type':'code'})">
                                    <i class="fa   fa-plus text-white   mx-2 "
                                       aria-hidden="true"></i>
                                </button>
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle" style="white-space: nowrap;">
                                <input type="text" class="form-control px-1  "
                                       placeholder="درصد تخفیف" v-model="param.discount_percent">
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle" style="white-space: nowrap;">
                                <input type="text" class="form-control px-1  "
                                       placeholder="حد تخفیف" v-model="param.limit_price">
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle" style="white-space: nowrap;">
                                <select class="px-1 my-1  form-control "

                                        v-model="param.user"
                                >
                                    <option value=""> عمومی</option>
                                    <option class="text-dark" v-for="user in users"
                                            :value="user.id">
                                        {{ '( ' + user.id + ' )' + user.name + ' ' + user.family }}

                                    </option>
                                </select>
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle" style="white-space: nowrap;">
                                <date-picker class="rounded-2 p-1   fromdate" inputClass="" :editable="true"

                                             inputFormat="YYYY/MM/DD" placeholder="تاریخ انقضا" color="#00acc1"
                                             v-model="param.expires_at"></date-picker>
                            </td>


                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VuePersianDatetimePicker from 'vue3-persian-datetime-picker';

    export default {
        props: ['admin', 'couponData', 'usersData', 'editLink', 'removeLink', 'createLink'],
        components: {
            datePicker: VuePersianDatetimePicker
        },
        data() {
            return {
                coupons: this.couponData ? JSON.parse(this.couponData) : [],
                users: this.usersData ? JSON.parse(this.usersData) : [],
                param: {user: ""},
                link: null,
                loading: false,
            }
        },
        mounted() {
//            document.querySelectorAll('.vpd-input-group').forEach((el) => {
//                el.classList.add('d-inline-block');
//            });

        },
        methods: {
            showDialog(type, message, click, params) {
                window.showDialog(type, message, onclick = () => click, params);
            },
            sendData(data) {
                this.loading = true;
                if (data.key)
                    data.key = f2e(data.key);
                if (data.value)
                    data.value = f2e(data.value);
                axios.post(this.link, data, {})

                    .then((response) => {
//                        console.log(response);
                            this.loading = false;

                            if (response.status === 200) {

                                if (data.type && data.type === 'code') {
                                    this.param.code = response.data;
                                    return;
                                }
                                this.coupons = response.data;
                                this.param = {};
                                window.showToast('success', 'با موفقیت انجام شد!');
                            }
//                            window.location.reload();


                        }
                    ).catch((error) => {
                    this.loading = false;

                    let errors = '';
                    if (error.response && error.response.status === 422)
                        for (let idx in error.response.data.errors)
                            errors += error.response.data.errors[idx] + '<br>';
                    else if (error.response && error.response.status === 403)
                        errors = error.response.data.message;
                    else {
                        errors = error;
                    }
                    window.showToast('danger', errors);

                });
            },
        },
    }
</script>
