<template>
    <div>
        <h3 class="text-center">All Posts</h3><br/>

        <table class="table table-bordered">
            <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Title</th>
                <!-- <th>Content</th> -->
                <th>Author</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>DeletePost</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="post in posts" :key="post.id">
                <!-- <td>{{ post.id }}</td> -->
                <td>
                    <router-link :to="{name: 'edit', params: { id: post.id }}">
                        {{ post.title }}
                    </router-link>
                </td>
                <!-- <td>{{ post.content }}</td> -->
                <td>{{ post.author }}</td>
                <td>{{ post.created_at }}</td>
                <td>{{ post.updated_at }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <!-- <router-link :to="{name: 'edit', params: { id: post.id }}" class="btn btn-primary">Edit
                        </router-link> -->
                        <button class="btn btn-danger" @click="deletePost(post.id)">Delete</button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                posts: []
            }
        },
        created() {
            this.axios
                .get('http://localhost:8000/api/posts')
                .then(response => {
                    this.posts = response.data;
                    // console.log(this.posts);
                });
            
        },
        methods: {
            deletePost(id) {
                this.axios
                    .delete(`http://localhost:8000/api/post/delete/${id}`)
                    .then(response => {
                        let i = this.posts.map(item => item.id).indexOf(id); //작성글의 id로 현재 글목록에서의 위치를 찾는다.
                        this.posts.splice(i, 1); //글 삭제
                    });
            }
        }
    }
</script>