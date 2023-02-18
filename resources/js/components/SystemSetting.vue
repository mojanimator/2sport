<template>
    <div class="   shadow-lg position-relative my-4">
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
                تنظیمات سیستم
            </div>
            <div class="card-body   px-0 p-sm-1  ">
                <div class="table-responsive-sm    start-0 end-0 overflow-x-scroll">
                    <table class="table  table-sm table-primary   table-striped table-light   "
                    >
                        <thead>
                        <tr>
                            <th class="small col-1 text-center" scope="col" style="white-space: nowrap;">#</th>
                            <th class="col-5  text-center">نام</th>
                            <th class="col-3  text-center">کلید</th>
                            <th class=" col-2   text-center">مقدار</th>
                            <th class=" col-3   text-center">عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="d,idx in setting" class=" " :key="d.id">
                            <th scope="row " class=" p-0 p-sm-1 text-center">{{d.id}}
                            </th>
                            <td class=" small p-0 p-sm-1 ">{{d.name}}</td>
                            <td class="small p-0 p-sm-1 text-center align-middle" style="white-space: nowrap;">
                                {{d.key}}
                            </td>
                            <td class=" small p-0 p-sm-1"><input type="text" class="form-control px-1  "
                                                                 v-model="d.value">
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle" style="white-space: nowrap;">
                                <div class="btn-group">
                                    <button title="ویرایش ردیف"
                                            class="btn btn-sm bg-secondary    p-1 hoverable-primary  "
                                            @click="link=editLink;param.id=d.id;param.value=d.value;  showDialog('confirm','از ویرایش اطمینان دارید؟', sendData ,param)">
                                        <i class="fa   fa-edit  text-white   mx-2 "
                                           aria-hidden="true"></i>
                                    </button>
                                    <button title="حذف ردیف" class="btn btn-sm bg-danger  p-1 hoverable-primary  "
                                            @click="link=removeLink;param.id=d.id;  showDialog('confirm','از حذف اطمینان دارید؟', sendData ,{id:d.id})">
                                        <i class="fa   fa-trash  text-white   mx-2 "
                                           aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class=" ">
                            <th scope="row " class=" p-0 p-sm-1 text-center"> #
                            </th>
                            <td class=" small p-0 p-sm-1 "><input type="text" class="form-control px-1  "
                                                                  placeholder="نام فارسی" v-model="param.name"></td>
                            <td class="small p-0 p-sm-1 text-center align-middle" style="white-space: nowrap;">
                                <input type="text" class="form-control px-1  "
                                       placeholder="کلید" v-model="param.key">
                            </td>
                            <td class=" small p-0 p-sm-1"><input type="text" class="form-control px-1  "
                                                                 placeholder="مقدار" v-model="param.value">
                            </td>
                            <td class="small p-0 p-sm-1 text-center align-middle" style="white-space: nowrap;">
                                <div class="btn-group">
                                    <button title="ساخت ردیف"
                                            class="btn btn-sm bg-success    p-1 hoverable-primary  "
                                            @click=" link=createLink; showDialog('confirm','از افزودن اطمینان دارید؟', sendData ,param)">
                                        <i class="fa   fa-plus-circle  text-white   mx-2 "
                                           aria-hidden="true"></i>
                                    </button>

                                </div>
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
    export default {
        props: ['settingData', 'editLink', 'removeLink', 'createLink'],
        data() {
            return {
                setting: this.settingData ? JSON.parse(this.settingData) : [],
                param: {},
                link: null,
                loading: false,
            }
        },
        mounted() {
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

                                this.setting = response.data;
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
