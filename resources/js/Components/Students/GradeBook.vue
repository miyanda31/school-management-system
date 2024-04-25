<template>
    <Layout>
        <Head><title>Eduket | Grade Book</title></Head>
        <page-header current-page="Student Grade Book" page="Book"/>
        <div  class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30 ml-auto mr-auto">
            <div class="card-box pd-20">
                <div class="profile-tab height-100-p">
                   <h5 class="h5">
                       {{user.name}} | Term {{term.number}} - {{term.year}}
                       <label class="pull-right">
                           <select @change="loadCategory" v-model="number" class="form-control input-sm ml-2 mr-2">
                               <option disabled value="">Term</option>
                               <option v-for="sub in terms" :value="sub.id" >T{{sub.number}}-{{sub.year}}</option>
                           </select>
                       </label>
                   </h5>
                    <table v-if="scores.length > 0" id="table" class="table table-hover table-bordered">
                        <thead>
                        <tr v-if="Number(user.number) < 4">
                            <th>#</th>
                            <th>Subject</th>
                            <th>CA1</th>
                            <th>CA2</th>
                            <th>ET</th>
                            <th>Final</th>
                            <th>Grade</th>
                            <th>Position</th>
                            <th>Remark</th>
                        </tr>

                        <tr v-else>
                            <th>#</th>
                            <th>Subject</th>
                            <th v-for="paper in papers">P{{paper.paper}}</th>
                            <th>Final</th>
                            <th>Grade</th>
                            <th>Position</th>
                            <th>Remark</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-if="Number(user.number) < 4" v-for="(score,index) in scores" :key="index">
                            <td>{{index+1}}</td>
                            <td>{{score.subject}}</td>
                            <td>
                                {{score.scores?score.scores.first:''}}
                            </td>
                            <td>
                                {{score.scores?score.scores.second:''}}
                            </td>
                            <td>
                                {{score.scores?score.scores.final:''}}
                            </td>

                            <td>{{score.score}}</td>
                            <td>{{score.grading && score.grading.grade?score.grading.grade:''}}</td>
                            <td>{{score.position}}</td>
                            <td>{{score.grading && score.grading?score.grading.remark:''}}</td>

                        </tr>
                        <tr v-else v-for="(score,index) in scores">
                            <td>{{index+1}}</td>
                            <td>{{score.subject}}</td>
                            <td v-for="paper in subjectPapers(score.subject_id)">{{score.scores?score.scores['P'+paper.paper]:''}}</td>
                            <td>{{score.score}}</td>
                            <td>{{score.grading && score.grading.grade?score.grading.grade:''}}</td>
                            <td>{{score.position}}</td>
                            <td>{{score.grading && score.grading?score.grading.remark:''}}</td>

                        </tr>

                        </tbody>
                    </table>

                    <length :category="scores" message="Results for student may not have been finalized for selected term please be patient"></length>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    name: "GradeBook",
    props: {
        term:{},
        terms:[],
        scores:[],
        user:{},
        papers:[]
    },
    data() {
        return {
            number: ''
        }
    },
    methods: {
        loadCategory() {
           this.$inertia.get(this.$route('grade-book.show',this.user.id),{t:this.number},{preserveState:true,preserveScroll:true})
        },
        subjectPapers(subject) {
            return this.papers.filter((sub)=>sub.subject_id === subject)
        }
    },
}
</script>

<style scoped>

</style>
