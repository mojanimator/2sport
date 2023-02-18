<template>

    <div class="   w-100">


        <!--                             dropdown data               -->
        <div :id="'hDropdown'+refId" class="dropdown-content ">
            <div class="input-group   ">
                <div class="input-group-text">
                    <i class="fa fa-search text-primary  "></i>
                </div>
                <input type="text" :placeholder="placeholder" v-model="sData" :id="'input_'+refId"
                       class=" py-1   form-control  "
                       @focus="openDropdown('h')"
                       @click="openDropdown('h')"
                       @blur="closeDropdown('h')"
                       @keypress.enter="closeDropdown('h')"
                       @keyup="selectData(sData,'key')">

                <div class=" input-group-text  " role='button'
                     @click="sData=''; selectData(sData,'clr');$root.$emit('search')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#e3342f"
                         class="bi bi-x-lg " viewBox="0 0 16 16">
                        <path
                            d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                    </svg>

                </div>
                <div v-if="newable!=null" class=" input-group-text bg-success " role='button'
                     @click=" createData(sData );$root.$emit('search')">
                    <svg v-show="!loading" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ffff"
                         class="bi bi-plus-lg"
                         viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                              d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                    </svg>
                    <div v-show="loading" class="spinner-border text-light" role="status"
                         style="width: 16px;height: 16px">
                        <span class="visually-hidden">Loading</span>
                    </div>

                </div>

            </div>
            <div v-if="error" class=" text-danger text-start small  col-12   " role="alert">
                <strong id=" ">{{ error }} </strong>
            </div>
            <ul class="list-group mb-5  hide" ref="listItems" :id="'list-data-docs-'+refId ">
                <li v-for="h,idx  in   this.filteredData" class="list-group-item  data-items noselect"
                    :id="refId+h['id']" :ref="refId+h['id']" :key="h['id']"
                    :class="{'active':data==h['name']}"
                    @mousedown.prevent="sData='';selectData(h,h['id'])"
                    :style="idx==0?'border-top-left-radius: 0;border-top-right-radius: 0':''">
                    {{ h['name'] }}
                </li>
            </ul>
        </div>
    </div>

</template>

<script>
let selectedBefore = false;
let selected = '';
export default {
    props: ['dataLink', 'createLink', 'for', 'multi', 'beforeSelected', 'refId', 'placeholder', 'newable', 'preSelect'],
    data() {
        return {
            sData: this.data ? this.data : '',
            loading: false,
            data_dropdown: null,
            province_input: null,

            filteredData: [],
            data: [],
            selected: [],
            activeData: [false],
            offset: -1, // in multi=false همه نمایندگی ها not exist
            backspace: false,
            params: [], //send dropdown params for search
            error: null,
        }
    },

    computed: {},


    mounted() {

//            console.log('drop');
        this.data_dropdown = document.querySelector('#list-data-docs-' + this.refId);
        this.data_input = document.querySelector('#input_' + this.refId);


        this.getData();
//************* data parameters

        this.data_input.addEventListener('keydown', (e) => {
            if (e.keyCode === 8) {
                this.backspace = true;
                this.searchCounty = '';
                this.filteredCounties = [];
                this.openDropdown('h');
            } else {
                this.backspace = false;
            }
        });


    },
    updated() {
//            this.getData();
        if (!this.data) {
            this.getData();

        }

//            console.log(this.filteredData);
    },
    created() {

    },
    methods: {


        async createData(data) {

            this.error = null;
            this.loading = true;
            await axios.post(this.createLink + '&new_item=' + data, null)
                .then((response) => {
//                        console.log(response);
                    this.data = response.data;
                    this.filteredData = this.data;
                    this.getData();
                }).catch((error) => {
                    // console.log(' error:');
                    // console.log(error);
                    this.error = error.response != null ? error.response.data : error;
                });

            this.loading = false;

        },
        async getData() {
            this.error = null;
            this.loading = true;
            await axios.get(this.dataLink, {})
                .then((response) => {
                    // console.log(response);
                    this.data = response.data;
                    this.filteredData = this.data;

                    if (this.preSelect) {
                        for (let i in this.data) {
                            if (this.data[i].id == this.preSelect)
                                this.sData = this.data[i].name;
                        }

                    }
                }).catch((error) => {
                    console.log(' error:');
                    console.log(error);
                });

            this.loading = false;
        },
        openDropdown(el) {
            if (el === 'h') {
                this.filteredData = this.data;
//                    console.log(this.data);
                this.data_dropdown.classList.remove('hide');

            }
        },
        closeDropdown(el) {

            if (el === 'h') {
                this.data_dropdown.classList.add('hide');
                this.filteredData = this.data;
                let i = 0;
                if (i < 4) {
                    if (this.multi == null && this.newable != null && this.data.filter(entry => {
                        return entry['name'].includes(this.sData);
                    }).length == 0) return; //is new item
                    this.sData = '';
                    for (let i in this.selected) {
                        this.sData += this.selected[i].name + ', ';
                    }
                    this.sData = this.sData.slice(0, this.sData.length - 2); //remove last ,
//                            console.log(this.sData);

                }

            }
        },

        selectData(h, hId) {
            if (hId === 'clr') {
                this.error = null;
//                    all.classList.remove('active');
                document.querySelectorAll('.data-items').forEach(e => e.classList.remove('active'));
//                    for (let i in this.activeData) {
//                        this.activeData[i] = false;
//                    }
                this.selected = [];
            } else if (hId === 'key') {

                this.filteredData = this.data.filter(entry => {
                    return entry['name'].includes(this.sData);
                });
//                    if (this.multi && this.filteredData[0]['name'].includes('همه'))
//                        this.filteredData.shift(); //remove first item (همه نمایندگی ها)
                if (this.sData === '' || this.sData === ' ')
                    this.filteredData = this.data;
                if (this.filteredData.length === 0)
                    this.filteredData = this.data;
                if (this.multi == null)
                    document.querySelectorAll('.data-items').forEach(e => e.classList.remove('active'));
            } else {

                this.selected = [];
                let item = document.querySelector('#' + this.refId + hId);

                if (this.multi == null) {
                    document.querySelectorAll('.data-items').forEach(e => e.classList.remove('active'));
                    document.querySelector('#input_' + this.refId).blur();
                }
                item.classList.toggle('active');
                for (let i = 0; i < this.data.length; i++) {
                    if (document.querySelector('#' + this.refId + this.data[i].id).classList.contains('active'))
                        this.selected.push(this.data[i]);
                }
                if (this.multi == null) {
//                        $("#dataInput").blur();
                    this.closeDropdown('h');
                }

            }
            this.$root.$emit('dropdown_click', {
                'group_id': this.selected.length > 0 ? this.selected[0].id : 1,
                'for': this.refId
            });
        },

    }

}


</script>
<style>
.hide {
    display: none;
}

.noselect {
    -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
    -khtml-user-select: none; /* Konqueror HTML */
    -moz-user-select: none; /* Old versions of Firefox */
    -ms-user-select: none; /* Internet Explorer/Edge */
    user-select: none;
    /* Non-prefixed version, currently
                                     supported by Chrome, Edge, Opera and Firefox */
}
</style>
