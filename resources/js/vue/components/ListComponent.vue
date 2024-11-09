<template>
    <router-link :to="{ name: 'save' }">Crear</router-link>
    <div>
        <h1>Post List</h1>

        <o-table :data="posts.data" :loading="isLoading">
            <o-table-column field="id" label="ID" v-slot="p">
                {{ p.row.id }}
            </o-table-column>
            <o-table-column field="title" label="Title" v-slot="p">
                {{ p.row.title }}
            </o-table-column>
            <o-table-column field="posted" label="Posted" v-slot="p">
                {{ p.row.posted }}
            </o-table-column>
            <o-table-column field="category_id" label="Categoria" v-slot="p">
                {{ p.row.category.title }}
            </o-table-column>
            <o-table-column field="category_id" label="Actions" v-slot="p">
                <router-link
                    :to="{ name: 'save', params: { slug: p.row.slug } }"
                    >Edit</router-link
                >
            </o-table-column>
        </o-table>
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
    },
};
</script>
