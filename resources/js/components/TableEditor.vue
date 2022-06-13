<template>
    <div class="card  position-absolute start-0 end-0 ">
        <div class="card-header text-center text-white bg-primary">
            <div class=" input-group my-2">
                <div
                        class="  ">{{mode == 'edit' ? 'ویرایش جدول' : 'جدول جدید'}}
                </div>
                <button v-if="mode=='edit'" class="btn btn-danger rounded ms-auto font-weight-bold" type="button"
                        id="addres-addon"
                        @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove )">
                    حذف
                </button>
            </div>
        </div>
        <div class="card-body ">
            <div v-show="tournament_id==null || (mode=='edit' && tournament_id==table.tournament.id)"
                 class="border border-1 rounded-3 p-3 bg-light">
                <div class="my-2   ">
                    <label for="tournament_name"
                           class="col-md-12    form-label  text-md-right">نام تورنومنت</label>
                    <input id="tournament_name" type="text" v-model="tournament_name"
                           class="  px-4 form-control   "
                           :class="errors.tournament_name? 'is-invalid':''"
                           name="tournament_name"
                           autocomplete="tournament_name" autofocus>

                </div>
                <div class=" text-danger text-start small     " role="alert">
                    <strong id="err-tournament_name"> </strong>
                </div>
                <div class="  mx-auto">

                    <select class="  my-2 form-control "
                            :class="errors.sport_id? 'is-invalid':''"
                            v-model="sport_id">

                        <option class="text-dark" :value="null">
                            انتخاب رشته ورزشی
                        </option>
                        <option class="text-dark" v-for="sport,idx in sports"
                                :value="sport.id">
                            {{ sport.name }}

                        </option>
                    </select>

                </div>
                <image-uploader
                        class=" col-sm-10 col-md-8 col-lg-6 mx-auto  overflow-x-scroll" id="img"
                        label="لوگوی تورنومنت"
                        :for-id="mode=='edit'?table.tournament.id:''" ref="imageUploader"
                        :crop-ratio="cropRatio"
                        :link="tournamentEditLink"
                        :id="mode=='edit'?table.tournament.id:''"
                        :data-id="mode=='edit'?table.id:''"
                        :replace="mode=='edit'"
                        :mode="mode==null?'create':mode"
                        :preload="img"
                        height="10">

                </image-uploader>
                <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-data.img">  </strong>
                                    </span>

            </div>
            <div class="row mx-auto ">

                <div class="  mx-auto">

                    <select class="  my-2 form-control "
                            :class="errors.tournament_id? 'is-invalid':''"
                            v-model="tournament_id">

                        <option class="text-dark" :value="null">
                            انتخاب تورنومنت
                        </option>
                        <option class="text-dark" v-for="tournament,idx in tournaments"
                                :value="tournament.id">
                            {{ tournament.name }}

                        </option>
                    </select>

                </div>

                <span class=" text-danger text-center small row  col-12" role="alert">
                          <strong id="err-tournament"> </strong>
                                    </span>


            </div>


            <div class="my-2   ">
                <label for="title"
                       class="col-md-12    form-label  text-md-right">تیتر جدول</label>
                <input id="title" type="text" v-model="title"
                       class="  px-4 form-control   "
                       :class="errors.title? 'is-invalid':''"
                       name="title"
                       autocomplete="title" autofocus>

            </div>
            <div class=" text-danger text-start small     " role="alert">
                <strong id="err-title"> </strong>
            </div>
            <!-- create rows and cols for table-->
            <div class="col-12   my-3">
                <div class="   " role="group" dir="rtl">
                    <!--<div class="btn p-2 bg-danger text-white m-0"-->
                    <!--title="حذف تصویر" data-bs-toggle="tooltip" data-bs-placement="bottom"-->
                    <!--@click=" refresh() ">-->
                    <!--<i class="fa fa-window-close text-white" aria-hidden="true"></i>-->
                    <!--</div>-->

                </div>
                <div class="table-responsive-sm overflow-visible ">
                    <table v-if="content"
                           class="table      table-bordered rounded table-sm">
                        <!--first row is header-->
                        <thead v-if="content.header" class="">
                        <tr class="bg-dark ">
                            <td v-for="col,idx in content.header" class="p-1 position-relative"
                                style="min-width: 80px">

                                <input type="text" v-model="content.header[idx]" class="form-control small px-1">
                                <div class="position-absolute end-0 top-0">
                                        <span class="btn p-1 small     bg-secondary hoverable-dark text-white    "
                                              title="نوع ستون" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                              @click="editTable(idx,'type')"
                                        >
                                        <i class="fa fa-eye text-white" aria-hidden="true"></i>
                                    </span>

                                    <span class="btn p-1  ms-1 bg-blue hoverable-dark text-white    "
                                          title="مرتب سازی" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                          @click="editTable(idx,'sort')"
                                    >
                                        <i class="fa fa-sort text-white" aria-hidden="true"></i>

                                    </span>
                                </div>
                            </td>
                        </tr>
                        </thead>

                        <tbody v-if="content.body">
                        <tr v-for="row,idx in content.body">
                            <template v-for="col,jdx in row" :key="col.id">


                                <!--first column is image-->
                                <td class="p-1   position-relative overflow-visible">
                                    <image-viewer v-if="col.type=='img'" height="2.5rem" class="" :id="'i'+col.id"
                                                  :preload="content.body[idx][jdx].value" :idx="idx" :jdx="jdx"
                                    ></image-viewer>

                                    <!-- is text-->

                                    <input v-else="" type="text" v-model="content.body[idx][jdx].value"
                                           class="form-control"
                                    >

                                    <div class="position-absolute end-0 top-0">
                                        <span class="btn p-1 bg-secondary hoverable-dark text-white    "
                                              title="ابزار" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                              @click="clearViews(); view[idx][jdx]=true"
                                        >
                                        <i class="fa fa-arrows-alt text-white" aria-hidden="true"></i>
                                    </span>
                                    </div>
                                    <div style="width: 10rem" v-show="view[idx][jdx]"
                                         class="card position-absolute bottom-0 end-0 bg-light"
                                         @click="clearViews()">
                                        <div class="flex-row   ">

                                        <span class="btn p-2 bg-danger hoverable-dark text-white m-1  "
                                              title="حذف سطر" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                              @click="delCell(idx,jdx,'row')  ">
                                        <i class="fas fa-minus-circle text-white" aria-hidden="true"></i>

                                    </span>
                                            <span class="btn p-2 bg-secondary hoverable-dark text-white m-1  "
                                                  title="انتقال بالا" data-bs-toggle="tooltip"
                                                  data-bs-placement="bottom"
                                                  @click="moveCell(idx,jdx,'up')  ">
                                        <i class="fa fa-arrow-up text-white" aria-hidden="true"></i>
                                    </span>
                                            <span class="btn p-2 bg-success hoverable-dark text-white m-1 "
                                                  title="درج بالا" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                  @click=" addCell(idx,jdx,'up') ">
                                        <i class="fa fa-arrow-up text-white" aria-hidden="true"></i>
                                        <i class="fa fa-plus text-white" aria-hidden="true"></i>
                                    </span>
                                            <div class="flex-fill"></div>

                                            <span class="btn p-2 bg-secondary hoverable-dark text-white m-1 "
                                                  title="انتقال راست" data-bs-toggle="tooltip"
                                                  data-bs-placement="bottom"
                                                  @click="moveCell(idx,jdx,'right')   ">
                                        <i class="fa fa-arrow-right text-white" aria-hidden="true"></i>
                                    </span>
                                            <span class="btn p-2 bg-success hoverable-dark text-white m-1  "
                                                  title="درج راست" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                  @click="addCell(idx,jdx,'right')   ">
                                        <i class="fa fa-angle-right text-white" aria-hidden="true"></i>
                                        <i class="fa fa-plus text-white" aria-hidden="true"></i>
                                    </span>
                                            <span class="btn p-2 bg-success hoverable-dark text-white m-1  "
                                                  title="درج چپ" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                  @click="addCell(idx,jdx,'left')   ">
                                        <i class="fa fa-plus text-white" aria-hidden="true"></i>
                                        <i class="fa fa-angle-left text-white" aria-hidden="true"></i>
                                    </span>

                                            <span class="btn p-2 bg-secondary hoverable-dark text-white m-1  "
                                                  title="انتقال چپ" data-bs-toggle="tooltip"
                                                  data-bs-placement="bottom"
                                                  @click=" moveCell(idx,jdx,'left')  ">
                                        <i class="fa fa-arrow-left text-white" aria-hidden="true"></i>
                                    </span>
                                            <div class="flex-fill"></div>

                                            <span class="btn p-2 bg-danger hoverable-dark text-white m-1  "
                                                  title="حذف ستون" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                  @click="delCell(idx,jdx,'col')  ">
                                        <i class="fas fa-minus-circle text-white fa-rotate-90" aria-hidden="true"></i>

                                    </span>
                                            <span class="btn p-2 bg-secondary hoverable-dark text-white m-1  "
                                                  title="انتقال پایین" data-bs-toggle="tooltip"
                                                  data-bs-placement="bottom"
                                                  @click=" moveCell(idx,jdx,'down')  ">
                                        <i class="fa fa-arrow-down text-white" aria-hidden="true"></i>
                                    </span>
                                            <span class="btn p-2 bg-success hoverable-dark text-white m-1 "
                                                  title="درج پایین" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                  @click=" addCell(idx,jdx,'down')  ">
                                        <i class="fa fa-arrow-down text-white" aria-hidden="true"></i>
                                        <i class="fa fa-plus text-white" aria-hidden="true"></i>
                                    </span>

                                        </div>

                                    </div>
                                </td>
                            </template>
                        </tr>
                        </tbody>
                    </table>
                    <div class=" text-danger text-start small     " role="alert">
                        <strong id="err-content"> </strong>
                    </div>
                </div>
            </div>

            <textarea v-model="tags" name="tags" id="tags" rows="2"
                      class="w-100 form-control my-1 px-4"
                      :class="errors.tags? 'is-invalid':''"
                      placeholder="تگ ها (با فاصله جدا شوند)"></textarea>


            <div v-if=" tags  " class=" form-control">
          <span v-for="t in tags.split(' ')"
                class="small bg-secondary text-white px-2 mx-1 my-1 rounded-pill d-inline-block ">{{t}}
          </span>

            </div>
            <div class="col-md-12  mt-2">
                <button @click=" sendData()" type="button"
                        class="btn btn-success btn-block font-weight-bold py-3">
                    ثبت
                </button>
            </div>
        </div>
    </div>
</template>

<script>


    import imageUploader from './imageUploader.vue';
    import imageViewer from './imageViewer.vue';

    export default {
        props: ['tableData', 'cropRatio', 'mode', 'imgLink', 'sendLink', 'tournamentEditLink', 'createLink', 'removeLink', 'sportData', 'tournamentData'],
        components: {imageUploader, imageViewer},
        data() {
            return {
                loading: false,

                tournaments: JSON.parse(this.tournamentData),
                sports: JSON.parse(this.sportData),
                sport_id: null,
                tournament_id: null,
                tournament_name: null,
                errors: {},
                category_id: null,
                tags: null,
                title: null,
                img: null,

                content: {
                    header: ['نام ستون', ''],
                    body:
                        [
                            [{id: this.getRandomInt(), type: 'img', 'value': null},
                                {id: this.getRandomInt(), type: 'txt', 'value': null},],

                        ]

                },
                view: [[false, false]],
                table: this.tableData ? JSON.parse(this.tableData) : null,
                toggleSort: false,

            }
        },

        mounted() {
            if (!this.category_id) this.category_id = "";
            if (!this.tournament_id) this.tournament_id = null;

            if (this.table) {
                if (this.table.tournament) {
                    this.tournament_name = this.table.tournament.name;
                    this.img = this.imgLink + '/' + this.table.tournament_id + '.jpg';

//                    this.$refs.imageUploader.id = this.table.tournament.id;
//                    this.$refs.imageUploader.link = this.tournamentEditLink;
                    this.$refs.imageUploader.doc = this.img;
                    this.sport_id = this.table.tournament.sport_id;
                }

                this.tournament_id = this.table.tournament_id;
                this.title = this.table.title;
                this.category_id = this.table.type_id;
                this.tags = this.table.tags;
                if (this.table.content) {
                    this.table.content = JSON.parse(this.table.content);


                    this.content = this.table.content.table;
                    this.view = this.content.body.map(e => e.map(e => false));
                }
            }


        },
        watch: {
            loading(val) {

                if (val === true) {
                    document.querySelector('#loading').classList.remove('d-none');
                } else {
                    document.querySelector('#loading').classList.add('d-none');
                }
            }

        },
        methods: {
            getRandomInt() {
                let min = 100000000;
                let max = 999999999;
                return Math.floor(Math.random() * (max - min) + min); //The maximum is exclusive and the minimum is inclusive
            },
            clearViews() {
                for (let i in this.view) {

                    for (let j in this.view[i]) {
                        this.view[i][j] = false;
                    }
                }

            },
            editTable(j, cmnd) {
                if (cmnd == 'type') {
                    for (let id in this.content.body) {

                        let tmp = this.content.body[id][j].type;

                        if (tmp == 'img')
                            this.content.body[id][j].type = 'txt';
                        if (tmp == 'txt')
                            this.content.body[id][j].type = 'img';


                    }
                } else if (cmnd == 'sort') {
                    this.toggleSort = !this.toggleSort;
                    this.content.body.sort(function (a, b) {

                        return a[j].value - b[j].value;
                    });
                    if (this.toggleSort)
                        this.content.body.reverse();
                }
            },
            delCell(i, j, side) {
                if (side == 'row') {
                    this.view.splice(i, 1);
                    this.content.body.splice(i, 1)
                }
                else if (side == 'col') {
                    for (let id in this.content.body) {
                        this.content.body[id].splice(j, 1);
                    }
                    this.content.header.splice(j, 1);
                    this.view[i].splice(j, 1);


                }
            },
            addCell(i, j, side) {
                function cell(e) {
                    return {

                        type: (e && e.type == 'img') ? 'img' : 'txt',
                        id: Math.floor(Math.random() * (999999999 - 10000000) + 10000000),
                        value: null

                    }
                }

                if (side === 'up') {
//                    for (let jd in this.content.body[i]) {
                    this.view.splice(i, 0, this.content.body[0].map((e) => false));
                    this.content.body.splice(i, 0, this.content.body[0].map(e => cell(e)))
//                    }
                }
                else if (side === 'down') {
//                    for (let jd in this.content.body[i]) {
                    this.view.splice(i + 1, 0, this.content.body[0].map((e) => false));
                    this.content.body.splice(i + 1, 0, this.content.body[0].map(e => cell(e)))

                } else if (side === 'right') {
                    for (let id in this.content.body) {
                        this.content.body[id].splice(j, 0, cell());
                    }
                    this.content.header.splice(j, 0, '');
                    this.view[i].splice(j, 0, false);


                } else if (side === 'left') {
                    for (let id in this.content.body) {
                        this.content.body[id].splice(j + 1, 0, cell());
                    }
                    this.content.header.splice(j + 1, 0, '');
                    this.view[i].splice(j + 1, 0, false);


                }

            },
            moveCell(i, j, side) {

                if (side === 'up') {
                    if (i === 0) return;
                    let tmp = this.content.body[i];
                    this.content.body[i] = this.content.body[i - 1];
                    this.content.body[i - 1] = tmp;
                }
                else if (side === 'down') {
                    if (i === this.content.body.length - 1) return;
                    let tmp = this.content.body[i];
                    this.content.body[i] = this.content.body[i + 1];
                    this.content.body[i + 1] = tmp;
                }

                else if (side === 'left') {
                    if (j === this.content.body[i].length - 1) return;

                    for (let id in this.content.body) {
                        for (let jd in this.content.body[id]) {

                            if (jd == j) {
                                let tmp = this.content.body[id][j];
                                this.content.body[id][j] = this.content.body[id][j + 1];
                                this.content.body[id][j + 1] = tmp;
                            }
                        }
                    }
                    let tmp = this.content.header[j];
                    this.content.header[j] = this.content.header[j + 1];
                    this.content.header[j + 1] = tmp;
                } else if (side === 'right') {

                    if (j === 0) return;

                    for (let id in this.content.body) {
                        for (let jd in this.content.body[id]) {

                            if (jd == j) {
                                let tmp = this.content.body[id][j];
                                this.content.body[id][j] = this.content.body[id][j - 1];
                                this.content.body[id][j - 1] = tmp;
                            }
                        }
                    }
                    let tmp = this.content.header[j];
                    this.content.header[j] = this.content.header[j - 1];
                    this.content.header[j - 1] = tmp;
                }


            },
            showDialog(type, message, click) {
                window.showDialog(type, message, onclick = () => click);
            },


            remove() {
                this.loading = true;
                axios.post(this.removeLink, {
                    id: this.table ? this.table.id : null,
//                    content: this.convertDataToHtml(await this.editor.save())
                })
                    .then((response) => {
//                            console.log(response);
                            this.loading = false;
                            if (response.status === 200)
                                window.location = '/panel/tables';

                        }
                    ).catch((error) => {
                    this.loading = false;
//                    console.log(error);
                    if (error.response) {
                        this.errors = error.response.data.errors || {};
                        invalidInputs(error.response.data.errors);
                    }
//                    if (error.response && error.response.status === 422)
//                        for (let idx in error.response.data.errors)
//                            this.errors += '' + error.response.data.errors[idx] + '<br>';
//                    else {
//                        this.errors = error;
//                    }
//                    window.showDialog('danger', this.errors, onclick = null);
                });
            }, async sendData() {
                validInputs();
                this.loading = true;
                let params = {
                    id: this.table ? this.table.id : null,
                    title: this.title,
                    sport_id: this.sport_id,
                    tournament_id: this.tournament_id,
                    tournament_name: this.tournament_name,
                    tags: this.tags,
                    data: {
                        table: this.content,

                    },

//                    content: this.convertDataToHtml(await this.editor.save())
                };
                if (this.tournament_id == null) {
                    params['img'] = this.$refs.imageUploader.getCroppedData();
                }
                axios.post(this.sendLink, params, {
                    onUploadProgress: function (progressEvent) {
//                        var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
//                        console.log(percentCompleted);
                    }
                })

                    .then((response) => {
                            this.loading = false;

                            if (response.status === 200)
                                if (this.mode !== 'edit')
                                    window.location = '/panel/tables';
                                else
                                    window.location.reload();
                            else {
                                window.showToast('danger', response, onclick = null);
                            }

                        }
                    ).catch((error) => {
                    this.loading = false;
//                    console.log(error);
                    if (error.response) {
                        this.errors = error.response.data.errors || {};
                        invalidInputs(error.response.data.errors);
                    }
                    let msg = ''
                    for (let idx in error.response.data.errors)
                        msg += '' + error.response.data.errors[idx] + '<br>';
//                    if (error.response && error.response.status === 422)
//                        for (let idx in error.response.data.errors)
//                            this.errors += '' + error.response.data.errors[idx] + '<br>';
//                    else {
//                        this.errors = error;
//                    }
//                    console.log(error);
//                    console.log(error.message);
                    window.showToast('danger', msg, onclick = null);
                });
            },
            log(str) {
                console.log(str);
            }
        }
    }
</script>
<style lang="scss">
    td {
        max-width: 2rem !important;
    }


</style>