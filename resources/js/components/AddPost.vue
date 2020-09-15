<template>
    <div>
        <h3 class="text-center">Add Post</h3>
        <div class="row">
            <div class="col-md-6" style="float:none; margin:0 auto">
                <form @submit.prevent="addPost">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" placeholder="제목을 입력해주세요." v-model="post.title">
                    </div>
                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" class="form-control" placeholder="작성자 성함을 입력해주세요." v-model="post.author">
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <p><textarea cols="150" rows="10" placeholder="글 내용을 입력해주세요." v-model="post.content" class="form-control"></textarea></p>
                    
                    </div>
                    <div class="float-right">
                        <button type="submit" class="btn btn-primary">Add Post</button>
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
        methods: {
            addPost() {

                this.axios
                    .post('http://localhost:8000/api/post/add', this.post)
                    .then(response => (
                        this.$router.push({name: 'home'})
                        // console.log(response.data)
                    ))
                    .catch(error => console.log(error))
                    .finally(() => this.loading = false)
            }
        }
    }
</script>