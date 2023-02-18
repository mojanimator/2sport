<template>
    <div class="card">
        <div class="card-header text-center text-white bg-primary">
            <div class=" input-group my-2">
                <div
                    class="  ">{{ mode == 'edit' ? 'ویرایش خبر' : 'خبر جدید' }}
                </div>
                <button v-if="mode=='edit'" class="btn btn-danger rounded ms-auto font-weight-bold" type="button"
                        id="addres-addon"
                        @click="  showDialog('confirm','از حذف اطمینان دارید؟', remove )">
                    حذف
                </button>
            </div>
        </div>
        <div class="card-body">
            <image-uploader
                class=" col-sm-10 col-md-8 col-lg-6 mx-auto  overflow-x-scroll" id="img"
                label="تصویر خبر"
                for-id="img" ref="imageUploader"
                :crop-ratio="1.25"
                link="null"
                :preload="img"
                height="10" mode="create">

            </image-uploader>
            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-img"> </strong>
                                    </span>
            <div class="row">
                <div class="col-sm-10 col-md-8 mx-auto">

                    <select class="px-4 my-2 form-control "
                            :class="errors.category_id? 'is-invalid':''"
                            v-model="category_id"
                    >
                        <option value="">دسته بندی</option>
                        <option class="text-dark" v-for="category in categories"
                                :value="category.id">
                            {{ category.name }}

                        </option>
                    </select>
                    <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-category_id"> </strong>
                                    </span>
                </div>
                <div class="col-sm-10 col-md-8  mx-auto ">
                    <div class="   align-self-center   my-1 ">
                        <div class="form-check  my-2">
                            <label class="form-check-label" for="is_draft_true">پیش نویس</label>
                            <input class="form-check-input" id="is_draft_true" type="checkbox"
                                   v-model="is_draft">
                        </div>

                        <div class="    d-flex"><span class=" ">انتشار</span>
                            <input type="number" id="publish_at" class="form-control d-inline mx-1 flex-shrink-1 "
                                   :disabled="is_draft"
                                   :class="errors.published_at? 'is-invalid':''"
                                   v-model="published_at">
                            <span class="w-100">ساعت بعد</span>

                        </div>


                    </div>
                    <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-published_at"> </strong>
                                    </span>

                </div>
            </div>


            <div class="my-2   ">
                <label for="title"
                       class="col-md-12    form-label  text-md-right">تیتر خبر</label>
                <input id="title" type="text" v-model="title"
                       class="  px-4 form-control   "
                       :class="errors.title? 'is-invalid':''"
                       name="title"
                       autocomplete="title" autofocus>

            </div>
            <div class=" text-danger text-start small     " role="alert">
                <strong id="err-title"> </strong>
            </div>

            <div class="my-2  ">
                <label for="summary"
                       class="col-md-12 col-form-label form-label  text-md-right">
                    خلاصه خبر
                </label>
                <textarea id="summary" rows="2" v-model="summary"
                          :class="errors.summary? 'is-invalid':''"
                          class="  px-4 form-control  "
                          name="summary"
                          autocomplete="summary" autofocus> </textarea>

                <div class=" text-danger text-start small     " role="alert">
                    <strong id="err-summary"> </strong>
                </div>
            </div>
            <label for="blog"
                   class="col-md-12 col-form-label form-label  text-md-right">
                متن خبر
            </label>

            <div id="blog" class=""></div>

            <tag-editor :data="tags" :classes="errors.tags? 'is-invalid':''" ref="tagEditor"></tag-editor>

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
import Paragraph from '@editorjs/paragraph' ;
import Header from '@editorjs/header';
import List from '@editorjs/list';
import EditorJS from '@editorjs/editorjs';
import ImageTool from '@editorjs/image';
import Marker from '@editorjs/marker';
import Quote from '@editorjs/quote';
import Table from '@editorjs/table';
//    import Table from 'chalkboard-test-table';
//    import Table from 'editorjs-table';
import LinkTool from '@editorjs/link';
import SimpleImage from '@editorjs/simple-image';
import Embed from '@editorjs/embed';
import AlignmentTuneTool from 'editorjs-text-alignment-blocktune';
//    let self;
import imageUploader from './imageUploader.vue';
import tagEditor from './tagEditor.vue';

export default {
    props: ['mode', 'blogData', 'oldTitle', 'oldSummary', 'oldImg', 'oldTags', 'corsHeader', 'sendLink', 'removeLink', 'categoryData'],
    components: {imageUploader, tagEditor},
    data() {
        return {
            loading: false,
            editor: null,
            title: this.oldTitle,
            summary: this.oldSummary,
            img: this.oldImg,
            tags: this.oldTags,
            category_id: null,
            published_at: 0,
            is_draft: false,
            content: null,
            blog: this.blogData ? JSON.parse(this.blogData) : null,
            categories: JSON.parse(this.categoryData),
            errors: {},

        }
    },

    mounted() {

        if (!this.category_id) this.category_id = "";

        if (this.blog) {
            this.title = this.blog.title;
            this.summary = this.blog.summary;
            this.is_draft = this.blog.is_draft;
            this.category_id = this.blog.category_id;
            this.title = this.blog.title;

            this.published_at = this.blog.published_at ? this.getPublishHour(this.blog.published_at) : 0;
        }
        this.initEditor('blog');

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
        showDialog(type, message, click) {
            window.showDialog(type, message, onclick = () => click);
        },
        async parseEditorData(content) {
            if (!content || content.content === null) return [];
            content = JSON.parse(content.content);

            for (let i in content) {
                if (content[i].type === 'image' && content[i].data && content[i].data.file) {
                    content[i].data.file.url = await this.getBase64(content[i].data.file.url, true);

                }
            }
            return content;
        },
        async getBase64(file, is_url = false) {

            if (is_url) {
                file = await (axios.get(file, {responseType: 'blob'}));
                file = file.data
            }
            let reader = new FileReader();
            return new Promise(resolve => {
                reader.onload = ev => {
                    resolve(ev.target.result)
                };
                reader.onerror = ev => {
                    resolve(null)
                };
                reader.readAsDataURL(file)
            });

        },
        getPublishHour(timestamp) {
            let today = new Date();
            let date = new Date(timestamp);


            let hour = date.getTime() - today.getTime();
            hour = Math.round(hour / 3600000);
            if (hour < 0) return 0;
            return hour;
        },
        remove() {
            this.loading = true;
            axios.post(this.removeLink, {
                id: this.blog ? this.blog.id : null,
//                    content: this.convertDataToHtml(await this.editor.save())
            })
                .then((response) => {
//                            console.log(response);
                        this.loading = false;
                        if (response.status === 200)
                            window.location = '/panel/blogs';

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
            axios.post(this.sendLink, {
                id: this.blog ? this.blog.id : null,
                title: this.title,
                summary: this.summary,
                img: this.$refs.imageUploader.getCroppedData(),
                category_id: this.category_id,
                is_draft: this.is_draft,
                published_at: this.published_at,
                tags: this.$refs.tagEditor.tags,
                blocks: JSON.stringify((await this.editor.save())['blocks']),

//                    content: this.convertDataToHtml(await this.editor.save())
            }, {
                onUploadProgress: function (progressEvent) {
//                        var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
//                        console.log(percentCompleted);
                }
            })

                .then((response) => {
//                            console.log(response);
                        this.loading = false;

                        if (response.status === 200)
                            window.location = this.mode === "edit" ? '/panel/blog/edit/' + this.blog.id : '/panel/blogs';

                    }
                ).catch((error) => {
                this.loading = false;
//                    console.log(error);
                if (error.response) {
                    this.errors = error.response.data.errors || {};
                    invalidInputs(error.response.data.errors);
                }
                let errors = '';
                invalidInputs(error.response.data.errors);
                if (error.response && error.response.status === 422)
                    for (let idx in error.response.data.errors)
                        errors += '' + error.response.data.errors[idx] + '<br>';
                else {
                    errors = error;
                }
                window.showToast('danger', errors);
            });
        },
        async initEditor(id) {


            this.editor = new EditorJS({
                    data:
                        {blocks: await this.parseEditorData(this.blog)}
                    ,
                    holder: id,
                    logLevel: 'ERROR',
                    onReady: (api, event) => {

                        document.querySelectorAll('.tc-table').forEach(el => el.dir = 'ltr');

//                            console.log(api);
                    },
                    onChange: (api, event) => {
                        if (event.type === 'block-added') {
                            document.querySelectorAll('.tc-table').forEach(el => el.dir = 'ltr');
                        }
//                            console.log(api);
                    },
//                        i18n: {
//                            direction: 'rtl',
//                        },
                    /**
                     * Available Tools list.
                     * Pass Tool's class or Settings object for each Tool you want to use
                     */
                    tools: {
                        paragraph: {
                            class: Paragraph,
                            inlineToolbar: true,
                        },
                        header: {
                            class: Header,
                            inlineToolbar: true,
                            shortcut: 'CMD+SHIFT+H',
                            config: {
                                placeholder: 'یک تیتر وارد کنید',
                                levels: [2, 3, 4],
                                defaultLevel: 3
                            }
                        },
                        Marker: {
                            class: Marker,
                            shortcut: 'CMD+SHIFT+M',
                        },
                        list: {
                            class: List,
                            inlineToolbar: true,
                        },
                        quote: {
                            class: Quote,
                            inlineToolbar: true,
                            shortcut: 'CMD+SHIFT+O',
                            config: {
                                quotePlaceholder: 'متن مهم',
                                captionPlaceholder: 'عنوان متن مهم',
                            },
                        },

//                        image: SimpleImage,
                        image: {
                            class: ImageTool,
                            inlineToolbar: true,
                            config: {
                                endpoints: {
                                    byFile: 'uploadFile', // Your backend file uploader endpoint
                                    byUrl: 'fetchUrl', // Your endpoint that provides uploading by Url
                                },
                                additionalRequestHeaders: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
//                                    actions: [
//                                        {
//                                            name: 'new_button',
//                                            icon: '<i class="fa fa-align-center"></i>',
//                                            title: 'title',
//                                            action: (name) => {
//                                                alert(`${name}   `);
//                                                //access corresponding block  ???
//                                                return false;
//                                            }
//                                        }
//                                    ],

                                uploader: {
                                    uploadByFile: async (file) => {


                                        // async because it expects a promise

//                                            const url = (window.URL || window.webkitURL).createObjectURL(file); // generate a blob in memory
                                        const base64 = await this.getBase64(file, false);

                                        return {
                                            success: 1,
                                            file: {
                                                url: base64,
                                                name: file.name,
                                                size: file.size,
                                                source: file // keep a reference to the original file
                                            }
                                        };


                                        // generate a blob in memory


                                    },
                                    /**
                                     * Upload file to the server and return an uploaded image data
                                     * @param {File} file - file selected from the device or pasted by drag-n-drop
                                     * @return {Promise.<{success, file: {url}}>}
                                     */
                                    uploadByFile2(file) {
                                        // your own uploading logic here
                                        return MyAjax.upload(file).then(() => {
                                            return {
                                                success: 1,
                                                file: {
                                                    url: 'https://codex.so/upload/redactor_images/o_80beea670e49f04931ce9e3b2122ac70.jpg',
                                                    // any other image data you want to store, such as width, height, color, extension, etc
                                                }
                                            };
                                        });
                                    },

                                    /**
                                     * Send URL-string to the server. Backend should load image by this URL and return an uploaded image data
                                     * @param {string} url - pasted image URL
                                     * @return {Promise.<{success, file: {url}}>}
                                     */
                                    uploadByUrl(url) {
                                        // your ajax request for uploading
                                        return MyAjax.upload(file).then(() => {
                                            return {
                                                success: 1,
                                                file: {
                                                    url: 'https://codex.so/upload/redactor_images/o_e48549d1855c7fc1807308dd14990126.jpg',
                                                    // any other image data you want to store, such as width, height, color, extension, etc
                                                }
                                            }
                                        })
                                    }
                                }
                            }
                        },
                        table: {
                            class: Table,
                            inlineToolbar: true,
                            config: {
                                rows: 2,
                                cols: 3,
                            },
                        },
                        linkTool: {
                            class: LinkTool,
                            config: {
                                endpoint: 'http://localhost:8008/fetchUrl', // Your backend endpoint for url data fetching,
                            }
                        },
                        embed: {
                            class: Embed,
                            inlineToolbar: true,
                            coservices: {
//                                youtube: true,
//                                coub: true,
                                codepen: {
                                    regex: /https?:\/\/codepen.io\/([^\/\?\&]*)\/pen\/([^\/\?\&]*)/,
                                    embedUrl: 'https://codepen.io/<%= remote_id %>?height=300&theme-id=0&default-tab=css,result&embed-version=2',
                                    html: "<iframe height='300' scrolling='no' frameborder='no' allowtransparency='true' allowfullscreen='true' style='width: 100%;'></iframe>",
                                    height: 300,
                                    width: 600,
                                    id: (groups) => groups.join('/embed/')
                                }
                            }
                        },
                        anyTuneName: {
                            class: AlignmentTuneTool,
                            config: {
                                default: "right",
                                blocks: {
                                    header: 'center',
                                    list: 'right',
                                    image: 'center',
                                }
                            },
                        }
                    },
                },
            )
            ;
        },
        convertDataToHtml(blocks) {
            let convertedHtml = "";
            blocks.map(block => {

                switch (block.type) {
                    case "header":
                        convertedHtml += `<h${block.data.level}>${block.data.text}</h${block.data.level}>`;
                        break;
                    case "embded":
                        convertedHtml += `<div><iframe width="560" height="315" src="${block.data.embed}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>`;
                        break;
                    case "paragraph":
                        convertedHtml += `<p>${block.data.text}</p>`;
                        break;
                    case "delimiter":
                        convertedHtml += "<hr />";
                        break;
                    case "image":
                        convertedHtml += `<img class="img-fluid" src="${block.data.file.url}" title="${block.data.caption}" /><br /><em>${block.data.caption}</em>`;
                        break;
                    case "list":
                        convertedHtml += "<ul>";
                        block.data.items.forEach(function (li) {
                            convertedHtml += `<li>${li}</li>`;
                        });
                        convertedHtml += "</ul>";
                        break;
                    default:
                        console.log("Unknown block type", block.type);
                        break;
                }
            });
            return convertedHtml;
        }
    }
}
</script>
<style lang="scss">
.tc-wrap {
    height: auto;
}

.ce-toolbar__settings-btn {
    width: 28px;
    height: 28px;
}
</style>
