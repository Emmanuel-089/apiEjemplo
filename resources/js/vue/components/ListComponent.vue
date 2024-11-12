<template>
    <div>
        <div class="title">
            <h1>Reporte General</h1>
        </div>
        <o-button
            label="Add"
            icon-left="plus"
            size="small"
            rounded
            @click="$router.push({ name: 'save' })"
            >Anadir</o-button
        >
        <div class="mb-5"></div>

        <o-modal v-model:active="confirmDeleteAction">
            <div class="p-9">
                <p>Seguro de eliminar?</p>
                <p></p>
                <div class="flex flex-row gap-2 bg-gray-100" p-3>
                    <o-button @click="confirmDeleteAction = false"
                        >Cancelar</o-button
                    >
                    <o-button variant="danger" @click="deletePost"
                        >Eliminar</o-button
                    >
                </div>
            </div>
        </o-modal>

        <o-table :data="posts.data" :loading="isLoading">
            <o-table-column field="id" label="ID" v-slot="p">
                {{ p.row.id }}
            </o-table-column>
            <o-table-column field="title" label="Titulo" v-slot="p">
                {{ p.row.title }}
            </o-table-column>
            <o-table-column field="posted" label="Posteado" v-slot="p">
                {{ p.row.posted }}
            </o-table-column>
            <o-table-column field="category_id" label="Categoria" v-slot="p">
                {{ p.row.category.title }}
            </o-table-column>
            <o-table-column field="category_id" label="Opciones" v-slot="p">
                <o-button
                    label="Warning"
                    variant="warning"
                    size="small"
                    rounded
                    @click="
                        $router.push({
                            name: 'save',
                            params: { slug: p.row.slug },
                        })
                    "
                    >Editar</o-button
                >

                <o-button
                    variant="danger"
                    size="small"
                    rounded
                    iconLeft="delete"
                    @click="
                        deletePostRow = p;
                        confirmDeleteAction = true;
                    "
                    >Eliminar</o-button
                >
            </o-table-column>
        </o-table>
        <div class="mb-5"></div>
        <o-pagination
            v-if="posts.data && posts.data.length > 0"
            @change="updatePage"
            :total="posts.total"
            v-model:current="currentPage"
            :range-beforce="2"
            :range-after="2"
            size="small"
            :simple="false"
            :rounded="true"
            :per-page="posts.per_page"
        >
        </o-pagination>
    </div>
</template>
<script>
export default {
    data() {
        return {
            posts: [],
            isLoading: true,
            currentPage: 1,
            confirmDeleteAction: false,
            deletePostRow: "",
        };
    },
    mounted() {
        this.listPage();
    },
    methods: {
        updatePage() {
            console.log(this.currentPage);
            setTimeout(() => {
                this.listPage();
            }, 50);
        },
        listPage() {
            console.log(this.currentPage);
            (this.isLoading = true),
                this.$axios
                    .get("/api/post?page=" + this.currentPage)
                    .then((res) => {
                        this.posts = res.data;
                        this.isLoading = false;
                    });
        },
        deletePost() {
            this.confirmDeleteAction = false;

            this.$oruga.notification.open({
                message: "Eliminado correctamente",
                position: "bottom-right",
                variant: "danger",
                duration: 4000,
                closable: true,
            });

            this.$axios.delete("/api/post/" + this.deletePostRow.row.id);
            this.posts.data.splice(this.deletePostRow.index, 1);
        },
    },
};
</script>
