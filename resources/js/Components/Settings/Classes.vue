<template>
    <Layout>
        <Head><title>Eduket | Classes</title></Head>
        <page-header current-page="Classes" page="Classes"/>
        <div class="card-box pd-20 height-100-p mb-30 col-md-8 ml-auto mr-auto">
            <h5 class="h5">Class Listing
<!--                <a v-if="classes.total === 0" data-toggle="modal" data-target="#exampleModal" href="" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i></a>-->
                <a @click="addClass" data-toggle="modal" data-target="#exampleModal" href="" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i></a>
            </h5>
            <pagination :previous-pages="previousPages" :next-pages="nextPages" :data="classes" ></pagination>
            <table  v-if="classes.total>0" class="table table-bordered table-sm">
                <thead>
                <tr>
                    <th>Form</th>
                    <th>Type</th>
                    <th>Shift</th>
                    <th>Students</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="cl in classes.data">
                    <td>{{cl.number}}</td>
                    <td>{{cl.name}}</td>
                    <td>{{cl.shift}}</td>
                    <td>{{cl.students}}</td>
                </tr>
                </tbody>
            </table>
            <empty :category="classes" message="No classes have been set please ensure this is done before students are able to access the system "></empty>
        </div>


        <Modal id="exampleModal" title="Create Classes" :submit="createClass">
            <template #body>
                <div class="form-group">
                    <label >Shift</label>
                    <input  placeholder="A or X depending on your school"  v-model="form.shift"  type="text" class="form-control"  >
                    <span class="text-danger" v-if="form.errors.stream">{{form.errors.stream}}</span>
                </div>

                <div class="form-group">
                    <label >Streams <small><span class="text-danger">*</span>Separate each category by a comma</small></label>
                    <input @input="sampleClasses" placeholder="write A,B,C etc resulting in 1A,1B class formation "  v-model="form.stream"   type="text" class="form-control"  >
                    <strong class="text-danger" v-if="samples.length>0">This will create classes like <span v-for="s in samples">1{{s}}, </span> for shift {{form.shift}}</strong>
                    <span class="text-danger" v-if="form.errors.stream">{{form.errors.stream}}</span>
                </div>
            </template>
            <template #footer>
                <button type="submit"  class="btn btn-primary">Create</button>
            </template>
        </Modal>

    </Layout>

</template>

<script>
export default {
    name: "Classes",
    props: {
        classes:{},
    },
    data() {
        return {
            form:this.$inertia.form({
                shift:'',
                stream:'',
            }),
            samples:[]
        }
    },

    methods: {
        addClass() {
            this.form.reset()
            this.samples = []
        },
        createClass() {
            this.form.post(this.$route('classes.store'),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    Swal.fire(
                        'Created!',
                        'Class system created',
                        'success',
                    )
                }
            })
        },
        sampleClasses() {
            this.samples = this.form.stream.split(',')
        },

        nextPages() {
            if (this.classes.next_page_url === null) {
                return;
            }

            this.$inertia.get(this.classes.next_page_url,{},{preserveState:true,preserveScroll:true})
        },

        previousPages() {
            if (this.classes.prev_page_url === null) {
                return;
            }

            this.$inertia.get(this.classes.prev_page_url,{},{preserveState:true,preserveScroll:true})
        },


    },
}
</script>

<style scoped>

</style>
