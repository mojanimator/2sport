<template>
    <div class="  bg-light rounded my-1  ">
        <div class="filter     rounded-3  px-2 ">


            <div v-show="false" :id="id" class="slider    " @update="uodateSlider2()"></div>

            <div class="row flex-row     ">

                <label class="col filter__label  px-0">
                    <input type="number" class="form-control  col ps-1"
                           :class="'filter__input-'+this.id"
                           @input=" " @keyup.enter="getData(1)">
                </label>
                <div class="col  small text-center align-self-center" style="font-size: 0.7rem">{{this.label}}</div>

                <label class="col filter__label px-0">
                    <input type="number" class="form-control col ps-1"
                           :class="'filter__input-'+this.id"
                           @input=" " @keyup.enter="getData(1)">
                </label>
            </div>
        </div>
    </div>
</template>

<script>
    import noUiSlider from 'nouislider';

    //    let self;

    export default {
        props: ['id', 'min', 'max', 'label', 'step', 'getData'],
        data() {
            return {
                minVal: this.min,
                maxVal: this.max,
                slider: null
            }
        },

        mounted() {

            self = this;
            this.slider = document.getElementById(this.id);


            const filterInputs = document.querySelectorAll('input.filter__input-' + this.id);

            noUiSlider.create(this.slider, {
                start: [this.minVal, this.maxVal],
                connect: true,
                tooltips: false,
//                pips: {
//                    mode: 'steps',
//                    stepped: true,
//                    density: 4
//                },
//                snap: true,
                step: this.step ? this.step : 1,
                range: {
                    'min': this.minVal,
                    'max': this.maxVal
                },

                // make numbers whole
                format: {
                    to: value => Math.round(value),
                    from: value => Math.round(value)
                }
            });

// bind inputs with noUiSlider
            this.slider.noUiSlider.on('update', (values, handle) => {
                this.maxVal = values[1];
                this.minVal = values[0];

//                self.$refs[self.id].minVal = values[0];
                filterInputs[handle].value = values[handle];
            });

            filterInputs.forEach((input, indexInput) => {

                input.addEventListener('change', () => {
                    this.slider.noUiSlider.setHandle(indexInput, input.value);
                    if (indexInput === 0)
                        this.minVal = input.value;
                    if (indexInput === 1)
                        this.maxVal = input.value;
                })
            });
        },
        watch: {
//            minVal(val) {
//                console.log(this.farsiDigit(val));
//                this.minVal = this.farsiDigit(val);
//            }

        },
        methods: {
//            farsiDigit(str) {
//                console.log(str);
//                if (!str) return str;
//                str = str.toString();
//                let eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
//                let per = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
//                for (let i in eng)
//                    str = str.replaceAll(eng[i], per[i]);
//                return str;
//
//            },
            updateSlider(idx) {

                this.slider.noUiSlider.setHandle(0, this.minVal);

                this.slider.noUiSlider.setHandle(1, this.maxVal);
//                this.$forceUpdate();
            }
        }
    }
</script>
<style lang="scss">
    @import "~nouislider";
    /* variables */
    $primary: #343265;
    $secondary: #343265;
    $gray: #999999;
    $gray-light: #DEDEDE;

    /* custom styles for UiSlider */
    .slider {
        height: 10px;
        /*padding: 0 16px;*/
    }

    .slider .noUi-connect {
        background: $primary;
    }

    .slider .noUi-handle {
        height: 18px;
        width: 18px;
        top: -5px;
        right: -9px; /* half the width */
        border-radius: 9px;
    }

    .noUi-target {
        cursor: pointer;

        :focus {
            outline: none;
        }
    }

    .noUi-horizontal .noUi-handle {
        top: -6px;
        right: -15px;
        width: 22px;
        height: 22px;
        background: $primary;
        border: 2px solid $secondary;
        border-radius: 50%;
        box-shadow: unset;
        cursor: pointer;
        transition: transform .1s;

        &:before, &:after {
            content: none
        }

        &:hover {
            transform: scale(1.1);
        }
    }

    .noUi-horizontal {
        height: 9px;
    }

    .noUi-connect {
        background: $primary;
    }

    .noUi-base {
        background: $gray-light;
    }

    .filter__input {
        /*height: 35px;*/
        border: 2px solid $secondary;
        border-radius: 5px;
        /*padding: 0 10px 0 40px;*/

        &:focus {
            background: rgba($primary, .2);;
        }
    }


</style>