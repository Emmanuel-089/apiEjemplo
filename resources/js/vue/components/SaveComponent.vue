<template>
    <h1 v-if="post">
        Actualizar <span class="font-bold">{{ post.title }}</span>
    </h1>
    <h1 v-else>
        Crear <span class="font-bold">{{ post.title }}</span>
    </h1>

    <div class="grid grid-cols-2 gap-3">
        <router-link :to="{ name: 'list' }">lista</router-link>
        <div class="col-span-2">
            <o-field
                label="Title"
                :variant="errors.title ? 'danger' : 'primary'"
                :message="errors.title"
            >
                <o-input v-model="form.title"></o-input>
            </o-field>
        </div>

        <o-field
            label="Content"
            :variant="errors.content ? 'danger' : 'primary'"
            :message="errors.content"
        >
            <o-input v-model="form.content" type="textarea"></o-input>
        </o-field>

        <o-field
            label="Description"
            :variant="errors.description ? 'danger' : 'primary'"
            :message="errors.description"
        >
            <o-input v-model="form.description" type="textarea"></o-input>
        </o-field>
        <o-field
            label="Slug"
            :variant="errors.slug ? 'danger' : 'primary'"
            :message="errors.slug"
        >
            <o-input v-model="form.slug"></o-input>
        </o-field>
        <o-field
            label="Posted"
            :variant="errors.posted ? 'danger' : 'primary'"
            :message="errors.posted"
        >
            <o-select v-model="form.posted" placeholder="">
                <option value="yes">Yes</option>
                <option value="not">No</option>
            </o-select>
        </o-field>

        <o-field
            label="Category"
            :variant="errors.category_id ? 'danger' : 'primary'"
            :message="errors.category_id"
        >
            <o-select v-model="form.category_id" placeholder="Selecciona">
                <option value=""></option>
                <option v-for="c in categories" v-bind:key="c.id" :value="c.id">
                    {{ c.title }}
                </option>
            </o-select>

            <o-field
                :message="fileError"
                :variant="fileError ? 'danger' : 'primary'"
            >
                <o-upload v-model="file">
                    <o-button tag="a" variant="primary">
                        <o-icon icon="upload"></o-icon>
                        <span>Click para Cargar</span>
                    </o-button>
                </o-upload>
                <o-button icon-left="upload" @click="upload"> Cargar </o-button>
            </o-field>
            <h3>Drag and drop</h3>
            <o-upload v-model="filesDragDrop" multiple drag-drop>
                <section>
                    <o-icon icon="upload"></o-icon>
                    <span>Arrastra y Suelta</span>
                </section>
            </o-upload>
            <p></p>
            <span v-for="(file, index) in filesDragDrop" :key="index">
                {{ file.name }}
            </span>
        </o-field>
    </div>
    <div class="mt-5"></div>

    <o-button label="Success" variant="success" @click="send">Send</o-button>
</template>
<script>
import { handleError } from "vue";
import { errorMessages } from "vue/compiler-sfc";

export default {
    async mounted() {
        if (this.$route.params.slug) {
            this.post = await this.$axios.get(
                "/api/post/slug/" + this.$route.params.slug
            );
            this.post = this.post.data;
            this.initPost();
        }

        console.log("Save");
        this.getCategory();
    },
    data() {
        return {
            post: "",
            form: {
                title: "",
                slug: "",
                description: "",
                content: "",
                category_id: "",
                posted: "",
            },
            errors: {
                title: "",
                slug: "",
                description: "",
                content: "",
                category_id: "",
                posted: "",
            },
            fileError: "",
            file: null,
            categories: [],
            filesDragDrop: [],
        };
    },
    methods: {
        initPost() {
            this.form.title = this.post.title;
            this.form.slug = this.post.slug;
            this.form.content = this.post.content;
            this.form.posted = this.post.posted;
            this.form.description = this.post.description;
            this.form.category_id = this.post.category_id;
        },
        upload() {
            this.fileError = "";
            const formData = new FormData();
            formData.append("image", this.file);
            this.$axios
                .post("/api/post/upload/" + this.post.id, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((res) => {
                    console.log(res);
                })
                .catch((err) => {
                    console.log(err);
                    this.fileError = err.response.data.message;
                });
        },
        cleanErrorsForm() {
            this.errors.title = "";
            this.errors.slug = "";
            this.errors.content = "";
            this.errors.posted = "";
            this.errors.description = "";
            this.errors.category_id = "";
        },
        getCategory() {
            this.$axios
                .get("/api/category")
                .then((res) => {
                    this.categories = res.data.data; // Asegúrate de acceder a los datos correctos
                })
                .catch((error) => {
                    console.error("Error fetching categories:", error);
                });
        },
        send() {
            this.cleanErrorsForm();
            if (this.post == "") {
                // Crear
                this.$axios
                    .post("/api/post", this.form)
                    .then((res) => {
                        console.log(res);
                        //Mensaje de creaaaaaaaaar
                        this.$oruga.notification.open({
                            message: "Creado correctamente",
                            position: "bottom-right",
                            duration: 3000,
                            closable: true,
                        });
                        this.$router.push({ name: "list" });
                    })
                    .catch((error) => {
                        const errors = error.response?.data?.errors || {}; // Verificar si 'errors' existe
                        if (errors.title) {
                            this.errors.title = errors.title[0];
                        }
                        if (errors.slug) {
                            this.errors.slug = errors.slug[0];
                        }
                        if (errors.description) {
                            this.errors.description = errors.description[0];
                        }
                        if (errors.posted) {
                            this.errors.posted = errors.posted[0];
                        }
                        if (errors.content) {
                            this.errors.content = errors.content[0];
                        }
                        if (errors.category_id) {
                            this.errors.category_id = errors.category_id[0];
                        }
                    });
            } else {
                // Actualizar
                this.$axios
                    .patch(`/api/post/${this.post.id}`, this.form)
                    .then((res) => {
                        console.log(res);
                        //
                        this.$oruga.notification.open({
                            message: "Actualizado correctamente",
                            position: "bottom-right",
                            duration: 3000,
                            closable: true,
                        });
                        this.$router.push({ name: "list" });
                    })
                    .catch((error) => {
                        const errors = error.response?.data?.errors || {}; // Verificar si 'errors' existe
                        if (errors.title) {
                            this.errors.title = errors.title[0];
                        }
                        if (errors.slug) {
                            this.errors.slug = errors.slug[0];
                        }
                        if (errors.description) {
                            this.errors.description = errors.description[0];
                        }
                        if (errors.posted) {
                            this.errors.posted = errors.posted[0];
                        }
                        if (errors.content) {
                            this.errors.content = errors.content[0];
                        }
                        if (errors.category_id) {
                            this.errors.category_id = errors.category_id[0];
                        }
                    });
            }
        },
    },
    watch: {
        filesDragDrop: {
            handler(val) {
                //return console.log(val[val.length - 1]);
                this.fileError = "";
                const formData = new FormData();
                formData.append("image", val[val.length - 1]);
                this.$axios
                    .post("/api/post/upload/" + this.post.id, formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((res) => {
                        console.log(res);
                    })
                    .catch((err) => {
                        console.log(err);
                        this.fileError = err.response.data.message;
                    });
            },
            deep: true,
        },
    },
};
</script>
