<template>

            <textarea v-model="tags" name="tags" id="tags" rows="2"
                      class="w-100 form-control my-1 px-4"
                      :class="classes"
                      placeholder="تگ ها (با فاصله جدا شوند)"></textarea>

    <div v-if=" tags" class=" form-control">
                  <span v-for="t in tags.split(' ')"
                        class="small bg-secondary text-white px-2 mx-1 my-1 rounded-pill d-inline-block ">{{ t }}
       </span>
    </div>
    <div class=" text-danger text-start small  col-12   " role="alert">
        <strong id="err-tags"> </strong>
    </div>
</template>

<script>
import imageUploader from "./imageUploader.vue";

export default {
    props: ['classes', 'data',],
    components: {},
    data() {
        return {
            tags: this.data
        }
    },
    mounted() {

    },
}
</script>

<style scoped>

</style>
