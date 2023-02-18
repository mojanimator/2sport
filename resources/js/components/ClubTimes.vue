<template>
    <div class="  small  ">
        <div v-for="t,idx in times.data" :key="idx"
             class="row my-1 position-relative  border-primary border-bottom mx-1">


            <div class="row  col-12 col-lg-6 mb-1 me-1">
                <div class="col-md-4  px-0 mb-1">

                    <select class=" form-control  " v-model="t.id" @input="log()">
                        <option value="" selected disabled hidden>ورزش</option>
                        <option v-for="s,idx in sports" :value="s.id" class=" ">{{s.name}}</option>
                    </select>
                </div>
                <div class=" col-md-4 col-6 px-0 mb-1">

                    <select class=" form-control  " v-model="t.d" @input="log()">
                        <option value="" selected disabled hidden>روز</option>
                        <option v-for="d,idx in days" :value="d.id" class=" ">{{d.name}}</option>
                    </select>
                </div>
                <div class=" col-md-4 col-6   px-0 mb-1">

                    <select class="   form-control " v-model="t.g" @input="log()">
                        <option value="" selected disabled hidden>جنسیت</option>
                        <option :value="genders[0].id" class=" ">{{genders[0].name}}</option>
                        <option :value="genders[1].id" class=" ">{{genders[1].name}}</option>
                    </select>
                </div>
            </div>

            <div class="row col-12  col-lg-6 mb-1">

                <div class="col  row input-group  ">

                    <select class="col   form-control px-1" v-model="t.fm" @input="log()">
                        <option value="" selected disabled hidden>از دقیقه</option>
                        <option v-for="fm,idx in minutes" :value="fm" class="    ">{{fm}}</option>
                    </select>
                    <select class="col   form-control px-1" v-model="t.fh" @input="log()">
                        <option value="" selected disabled hidden>از ساعت</option>
                        <option v-for="fh,idx in hours" :value="fh" class="    ">{{fh}}</option>
                    </select>
                </div>

                <div class="col  row input-group ms-1">

                    <select class="col   form-control px-1" v-model="t.tm" @input="log()">
                        <option value="" selected disabled hidden>تا دقیقه</option>
                        <option v-for="tm,idx in minutes" :value="tm" class="   ">{{tm}}</option>
                    </select>
                    <select class="col   form-control px-1" v-model="t.th" @input="log()">
                        <option value="" selected disabled hidden>تا ساعت</option>
                        <option v-for="th,idx in hours" :value="th" class="    ">{{th}}</option>
                    </select>
                </div>

            </div>

            <div class="w-auto px-0  text-danger move-on-hover  position-absolute  end-0   "
                 @click="removeItem(idx)">
                <i class="fa fa-2x fa-window-close   " aria-hidden="true"></i>
            </div>

        </div>
        <div class="  text-end ">
            <div class="btn btn-success   mt-2  " @click="addItem()">
                <i class="fa fa-plus  my-1 mx-0" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['data', 'preload'],
        components: {},
        data() {
            return {
                sports: [],
                times:
                    {
                        'data': [{'id': '', 'g': '', 'd': '', 'fh': '', 'fm': '', 'th': '', 'tm': ''}],
                        'genders': [],
                        'sports': [],
                    }

                , //sport_id,gender,day,from,to
                days: [
                    {'id': 7, 'name': 'هر روز'},
                    {'id': 0, 'name': 'شنبه'},
                    {'id': 1, 'name': 'یک شنبه'},
                    {'id': 2, 'name': 'دو شنبه'},
                    {'id': 3, 'name': 'سه شنبه'},
                    {'id': 4, 'name': 'چهار شنبه'},
                    {'id': 5, 'name': 'پنج  شنبه'},
                    {'id': 6, 'name': 'جمعه'},
                ],
                genders: [
                    {'id': 0, 'name': 'آقایان'},
                    {'id': 1, 'name': 'بانوان'},
                ],
                hours: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
                minutes: [0, 30,],

            }
        },
        watch: {},
        created() {
            this.sports = JSON.parse(this.data);
            if (this.preload) {
                this.times.data = JSON.parse(this.preload);
            }
        },
        mounted() {
        },
        methods: {

            log() {

                console.log();
            },
            addItem() {
                this.times.data.push({'id': '', 'g': '', 'd': '', 'fh': '', 'fm': '', 'th': '', 'tm': ''});
            },
            removeItem(idx) {
                this.times.data.splice(idx, 1);
            },
            getTimes() {

                return JSON.stringify(this.times.data);
                for (let i in this.times.data) {
                    if (!this.times.genders.includes(this.times.data[i].g)) {
                        this.times.genders.push(this.times.data[i].g);
                    }

                }
            }
        }
    }
</script>
