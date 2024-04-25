<template>
    <Layout bck="bg-white box-shadow">
        <div  class="mailbox-controls">
            <label style="display: inline-flex;" class="pull-left">&nbsp;
                <select @change="loadBooks"  v-model="entries" class="form-control input-sm">
                    <option value="" disabled>Show</option>
                    <option v-for="i in 10">{{i*10}}</option>
                </select>
            </label>
            <label style="display: inline-flex;" class="pull-left">&nbsp;
                <select @change="loadBooks" v-model="category"  class="form-control input-sm"  >
                    <option value="" disabled>Category</option>
                    <option>Long Loan</option>
                    <option>Short Loan</option>
                    <option>Reference</option>
                </select>
            </label>
            <label style="display: inline-flex;" class="pull-left">&nbsp;
                <select @change="loadBooks" v-model="sub" class="form-control input-sm">
                    <option value="" disabled>Subject</option>
                    <option v-for="sub in subjects" :value="sub.id">{{sub.short_code}}</option>
                </select>
            </label>
            <label style="display: inline-flex;" class="pull-left">&nbsp;
                <select @change="loadBooks" v-model="form" class="form-control input-sm">
                    <option value="" disabled>Class</option>
                    <option v-for="sub in 4" >{{sub }}</option>
                </select>
            </label>

            <div v-if="books.total>0" class="pull-right">
                {{books.current_page + ' of '+books.last_page}}
                <div class="btn-group">
                    <button  @click="previousPages" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                    <button @click="nextPages" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                </div>
                <!-- /.btn-group -->
            </div>
        </div>

        <div v-if="books.total>0" class="table-responsive">
            <table class="table">
                <tbody>
                <tr>
                    <th>Name</th>
                    <th >Subject</th>
                    <th >Author</th>
                    <th>Class</th>
                    <th>Number</th>
                    <th>Category</th>
                    <th></th>
                </tr>
                <tr :id="'book'+book.id" v-for="book in books.data">
                    <td>
                        <sup><label v-if="book.status === 'Pending'" class="badge badge-primary">R</label></sup>&nbsp;
                        {{book.book?book.book.title:''}}
                    </td>
                    <td>{{book.book?book.book.subject.name:''}}</td>
                    <td>{{book.book?book.book.author.fname+' '+book.book.author.lname:''}}</td>
                    <td>{{book.book?book.book.class:''}}</td>

                    <td>
                        {{book.number}}
                    </td>
                    <td>
                        {{book.book?book.book.category:''}}
                    </td>
                    <td>
                        <a v-if="book.status === 'Available'" data-toggle="modal" data-target="#issue" href="" class="btn btn-sm btn-primary" @click.prevent="loadBook(book.id)">Issue</a>
                        <a v-if="book.status === 'Reserved'" data-toggle="modal" data-target="#issue" href="" class="btn btn-sm btn-success" @click.prevent="issueNow(book)">Reserved</a>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

        <empty :category="books" message="No books were found under the current selection. It may also mean that no books have been added yet. Please ensure there are books in the software"></empty>

        <!-- Modal -->
        <div class="modal fade" id="issue" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Issue Book</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                    <form @submit.prevent="issueBook" method="post">
                    <div class="modal-body  white-box">
                        <div v-if="reservation" class="col-md-12">
                            <div class="user-box">
                                <div class="user-img">
                                    <img :src="'/img/'+reservation.avatar" :alt="reservation.avatar">
                                </div>
                                <div class="user-info">
                                    <h4>{{reservation.name}}</h4>
                                    <p>Has made a reservation for the book to be made available on <strong>{{reservation.date | myDate}}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div  v-if="!reservation" class="form-group">
                            <label >Issue to: </label>
                            <multiselect  @searchable="true" @search-change="searchUser" placeholder="Search User" track-by="id" label="name" v-model="user" :options="users"></multiselect>
                        </div>

                        <div class="form-group">
                            <label  >Return Date</label>
                            <input class="form-control" type="date" v-model:name="form2.date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Issue</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    name: "Issuing",
    props: {
        books:[],

        subjects:[],
    },
    data() {
        return {
            user:'',
            title:false,
            reservation:'',
            users:[],
            category:'',
            status:'',
            sub:'',
            entries:'',
            form:'',
            id:'',
            form2:this.$inertia.form({
                user:null,
                date:null,
                id:null,
            }),
        }
    },

    methods: {

        loadBooks() {
            this.$inertia.get(this.$route('issues.index'),{'t':this.sub,'n':this.entries,'f':this.form,'c':this.category},{preserveState:true})
        },

        nextPages() {
            if (this.books.next_page_url === null) {
                return;
            }


            this.$inertia.get(this.books.next_page_url)
        },

        previousPages() {
            if (this.books.prev_page_url === null) {
                return;
            }

            this.$inertia.get(this.books.prev_page_url)
        },

        searchUser(user){
              if (user) {
                  axios.get(this.$route('issues.show',[user,{u:true}])).then(({data})=>{
                      this.users = data;
                  })
              }
        },
        issueNow(book){
            this.reservation = book.reservation
            this.form2.id = book.id
        },
        loadBook(book){
            this.form2.id = book
            this.reservation =''
        },

        issueBook(){
            this.form2.transform((data)=>({
                ...data,
                user:this.user.id
            })).post(this.$route('issues.store'),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#issue').modal('hide');
                    swal(
                        'Issued!',
                        'Book was issued to '+this.user.name,
                        'success'
                    )
                }
            })
        }
    },


}
</script>

<style scoped>

</style>
