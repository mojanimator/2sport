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
        <div class="   ">
            <div class="card card-header bg-primary text-white">
                گزارش سیستم
            </div>


            <div class="card-body   px-0 p-sm-1  ">
                <!--chart section-->
                <div class="card ">
                    <div class="card-header bg-light ">

                        <div class="form-check form-check-inline">
                            <input checked
                                   @change="updateTypes('بازیکن')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox1"
                                   value="بازیکن"/>
                            <label class="form-check-label" for="inlineCheckbox1">بازیکن</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input checked
                                   @change="updateTypes('مربی')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox2"
                                   value="مربی"/>
                            <label class="form-check-label" for="inlineCheckbox2">مربی</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input checked
                                   @change="updateTypes('باشگاه')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox3"
                                   value="باشگاه"/>
                            <label class="form-check-label" for="inlineCheckbox3">باشگاه</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input checked
                                   @change="updateTypes('فروشگاه')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox4"
                                   value="فروشگاه"/>
                            <label class="form-check-label" for="inlineCheckbox4">فروشگاه</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input :disabled="params.type=='مالی'" checked
                                   @change="updateTypes('محصول')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox5"
                                   value="محصول"/>
                            <label class="form-check-label" for="inlineCheckbox5">محصول</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input :disabled="params.type=='مالی'" checked
                                   @change="updateTypes('خبر')" class="form-check-input"
                                   type="checkbox" id="inlineCheckbox6"
                                   value="خبر"/>
                            <label class="form-check-label" for="inlineCheckbox6">خبر</label>
                        </div>
                        <div class="btn-group   my-2 ">

                            <input @change="params.type='تعداد';getData()" v-model="params.type" value="تعداد"
                                   type="radio"
                                   class="btn-check"
                                   name="gender"
                                   id="type-count"
                                   autocomplete="off"/>
                            <label class="btn btn-outline-primary px-sm-3" for="type-count">تعداد</label>
                            <input @change="params.type='مالی';getData()" v-model="params.type" value="مالی"
                                   type="radio"
                                   class="btn-check"
                                   name="gender"
                                   id="type-pay"
                                   autocomplete="off"/>
                            <label class="btn btn-outline-primary px-sm-3" for="type-pay">مالی</label>


                        </div>

                        <div class="row     mx-auto">
                            <date-picker class="rounded-2 p-1 col-md-6 fromdate" inputClass="" :editable="true"

                                         inputFormat="YYYY/MM/DD" placeholder="از تاریخ" color="#00acc1"
                                         v-model="params.dateFrom" @change="getData()"></date-picker>
                            <date-picker class="rounded-2 p-1 col-md-6 todate" inputClass="" :editable="true"

                                         inputFormat="YYYY/MM/DD" placeholder="تا تاریخ" color="#dd77dd"
                                         v-model="params.dateTo" @change="getData()"></date-picker>
                            <div class="col-md-6 p-1">
                                <select class="   form-control "

                                        v-model="params.province"
                                        @change="params.county='';getData()">
                                    <option value=""> استان</option>
                                    <option class="text-dark" v-for="province in provinces"
                                            :value="province.id">
                                        {{ province.name }}

                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <canvas class="w-100 px-0 p-sm-4" id="myChart" height="400"></canvas>
                </div>
                <!--table section-->
                <div class="table-responsive position-absolute    start-0 end-0 overflow-x-scroll">
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
                        <tr v-for="d,idx in []" class=" " :key="d.id">
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
                                            @click="  showDialog('confirm','از ویرایش اطمینان دارید؟', sendData ,param)">
                                        <i class="fa   fa-edit  text-white   mx-2 "
                                           aria-hidden="true"></i>
                                    </button>
                                    <button title="حذف ردیف" class="btn btn-sm bg-danger  p-1 hoverable-primary  "
                                            @click="  showDialog('confirm','از حذف اطمینان دارید؟', sendData ,{id:d.id})">
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
                                                                  placeholder="نام فارسی"></td>
                            <td class="small p-0 p-sm-1 text-center align-middle" style="white-space: nowrap;">
                                <input type="text" class="form-control px-1  "
                                       placeholder="کلید">
                            </td>
                            <td class=" small p-0 p-sm-1"><input type="text" class="form-control px-1  "
                                                                 placeholder="مقدار">
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
    import Chart from 'chart.js/auto';
    import VuePersianDatetimePicker from 'vue3-persian-datetime-picker';
    import {shallowRef} from 'vue';

    let colors = ['rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'];
    export default {
        props: ['provinceData', 'countyData', 'logLink',],
        components: {
            datePicker: VuePersianDatetimePicker
        },
        data() {
            return {
                data: [],
                params: {province: "", type: 'تعداد', types: ['بازیکن', 'مربی', 'باشگاه', 'فروشگاه', 'محصول', 'خبر',]},
                link: null,
                loading: false,
                provinces: JSON.parse(this.provinceData),
                counties: JSON.parse(this.countyData),
                chart: null,

            }
        },
        mounted() {
            document.querySelector('.fromdate label').append('از تاریخ');
            document.querySelector('.todate label').append('تا تاریخ');

            document.querySelectorAll('.vpd-input-group').forEach((el) => {
                el.classList.add('d-inline');
            });
            document.querySelectorAll('.vpd-input-group input').forEach((el) => {
                el.classList.add('w-100');
            });
            this.params.dateFrom = this.date('yesterday');
            this.params.dateTo = this.date();
            this.initChart();
            this.getData();
        },
        methods: {
            showDialog(type, message, click, params) {
                window.showDialog(type, message, onclick = () => click, params);
            },
            updateTypes(type) {
                var index = this.params.types.indexOf(type);
                if (index !== -1) {
                    this.params.types.splice(index, 1);
                } else {
                    this.params.types.push(type);
                }
                this.getData();
            },
            initChart() {
                const ctx = document.getElementById('myChart').getContext('2d');
                this.chart = shallowRef(new Chart(ctx, {
                    type: 'line',
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'نمودار گزارشات'
                            },
                        },
                        interaction: {
                            intersect: false,
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true
                                }
                            },
                            y: {
                                beginAtZero: true,
                                display: true,
                                title: {
                                    display: true,
                                    text: 'تعداد'
                                },
//                                suggestedMin: -10,
//                                suggestedMax: 200
                            }
                        }
                    },
                    data: {},

                }));
            },
            updateChart() {
                let isPayment = this.params.types.filter((el) => el === 'پرداخت'
                ).length > 0;
                this.chart.data.datasets = [];

                let ix = -1;
                for (let i in this.data.data) {
                    ix++;
                    let counts = [];
                    if (this.params.type === 'تعداد')
                        for (let idx in this.data.dates) {
                            if (!(this.data.dates[idx] in this.data.data[i])) {
                                this.data.data[i][this.data.dates[idx]] = [];
                            }
                            counts.push(this.data.data[i][this.data.dates[idx]].length);
                        }
                    else if (this.params.type === 'مالی')
                        for (let idx in this.data.dates) {
                            if (!(this.data.dates[idx] in this.data.data[i])) {
                                this.data.data[i][this.data.dates[idx]] = [];
                            }

                            counts.push(this.data.data[i][this.data.dates[idx]].reduce((partialSum, a) => partialSum + a.amount, 0));
                        }

//                    var r = Math.floor(Math.random() * 255);
//                    var g = Math.floor(Math.random() * 255);
//                    var b = Math.floor(Math.random() * 255);
                    this.chart.data.datasets.push({
                        label: i,
//                        cubicInterpolationMode: 'monotone',
                        borderWidth: 1,
                        data: counts,
//                        borderColor: "rgb(" + r + "," + g + "," + b + ")",
                        borderColor: colors[ix],
                        backgroundColor: colors[ix],
                        tension: 0.4,
                    });
                }
                this.chart.data.labels = e2f(this.data.dates);

                this.chart.options.scales.y.title.text = this.params.type === 'مالی'
                    ? 'پرداخت (تومان)' : 'تعداد';
                this.chart.update();
            },
            getData() {
                this.loading = true;

                axios.get(this.logLink, {
                    params: this.params
                })

                    .then((response) => {
//                            console.log(response.data);
                            this.loading = false;

                            if (response.status === 200) {
                                this.data = response.data;
                                this.updateChart();
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
            date(day) {
                var t = new Date().getTime();
                if (day && day === 'yesterday')
                    var today = new Date(t - 24 * 60 * 60 * 1000);
                else
                    var today = new Date(t + 24 * 60 * 60 * 1000);
//                var dd = String(today.getDate()).padStart(2, '0');
//                var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
//                var yyyy = today.getFullYear();
//                return yyyy + '/' + mm + '/' + dd;
                return f2e(today.toLocaleDateString('fa-IR'));
            },
            log(str) {
                console.log(str);
            }
        },
    }
</script>
