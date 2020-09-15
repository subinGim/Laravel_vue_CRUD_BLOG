<template>
    <div>
        <h3 class="text-center">Edit Post</h3>
        <div class="row">
            <div class="col-md-6" style="float:none; margin:0 auto">
                <form @submit.prevent="updatePost">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" v-model="post.title">
                    </div>
                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" class="form-control" v-model="post.author">
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <p><textarea cols="150" rows="10" v-model="post.content" class="form-control"></textarea></p>
                        <!-- <input type="textarea" class="form-control" v-model="post.content"> -->
                    </div>
                    <div class="float-right">
                        <button type="submit" class="btn btn-primary">Update Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                post: {}
            }
        },
        created() {
            this.axios
                .get(`http://localhost:8000/api/post/edit/${this.$route.params.id}`)
                .then((response) => {
                    this.post = response.data;
                    // console.log(this.post);
                });
        },
        methods: {
            updatePost() {
                this.axios
                    .post(`http://localhost:8000/api/post/update/${this.$route.params.id}`, this.post)
                    .then((response) => {
                        this.$router.push({name: 'home'});
                    });
            }
        }
    }
</script>