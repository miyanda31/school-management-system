<template>
    <Layout>
        <Head><title>Eduket | Report</title></Head>
        <page-header current-page="School Report" page="Reports"/>
        <div class="invoice-wrap">
            <div class="invoice-box">
                <div class="invoice-header">

                </div>
                <h4 class="text-center mb-30 weight-600">SCHOOL REPORT</h4>
                <div class="row pb-30">
                    <div class="col-md-4">
                        <h5 class="mb-15">{{user.name}}</h5>
                        <p class="font-14 mb-5">{{user.student_id}}</p>
                        <p class="font-14 mb-5">Form {{form.number + form.name}}</p>
                        <p class="font-14 mb-5"><span class="weight-600">Enrollment:</span> {{total}}</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="logo text-center">
                            <img width="70%" src="/img/graduation.png" alt="">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-right">
                            <p class="font-14 mb-5"><strong>{{school.name}}</strong></p>
                            <p class="font-14 mb-5">{{school.address}}</p>
                            <p class="font-14 mb-5">{{school.phone}}</p>
                            <p class="font-14 mb-5">{{school.email}}</p>
                        </div>
                    </div>
                </div>
                <div class="invoice-desc pb-30">
                    <div v-if="scores" class="row mt-2 text-center">
                        <div class="col-md-3 ">
                          <span class="weight-600">Score: </span> {{scores.score}}
                        </div>
                        <div class="col-md-3">
                            <span class="weight-600">Aggregate:</span> <span v-if="user.written>=6">{{scores .aggregate}}</span>
                        </div>
                        <div class="col-md-3">
                           <span class="weight-600">Position:</span> {{scores.position}}
                                <span v-if="scores.position === 1" class="fa fa-trophy" style="color: #ffdf00;"></span>
                                <span v-else-if="scores.position === 2" class="fa fa-trophy" style="color: #c0c0c0;"></span>
                                <span v-else-if="scores.position === 3" class="fa fa-trophy" style="color: #cd7f32;"></span>

                        </div>
                        <div class="col-md-3">
                            <span class="weight-600">Remark:</span> <span v-if="scores.aggregate>40">Pass</span><span v-else>Fail</span>
                        </div>
                    </div>
                    <table  class="table text-uppercase table-sm table-bordered mt-2">
                        <thead>
                        <tr>
                            <th></th>
                            <th v-if="form.number < 4" >CA1</th>
                            <th v-if="form.number < 4" >CA2</th>
                            <th v-if="form.number < 4" >EOT</th>
                            <th v-if="form.number === 4">P1</th>
                            <th v-if="form.number === 4">P2</th>
                            <th v-if="form.number === 4">P3</th>
                            <th>SCORE</th>
                            <th>Agg</th>
                            <th>POS</th>
                            <th>REMark</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="grade in user.grades">
                            <td>{{grade.subject.name}}</td>
                            <td v-if="form.number < 4" >{{grade.scores?grade.scores.first:''}}</td>

                            <td v-if="form.number < 4">{{grade.scores?grade.scores.second:''}}</td>

                            <td v-if="form.number < 4">{{grade.scores?grade.scores.final:''}}</td>

                            <td v-if="form.number === 4"> {{grade.scores?grade.scores['P1']:''}}</td>
                            <td v-if="form.number === 4"> {{grade.scores?grade.scores['P2']:''}}</td>
                            <td v-if="form.number === 4"> {{grade.scores?grade.scores['P3']:''}}</td>

                            <td>{{grade.score}}</td>
                            <td>{{grade.grading?grade.grading.grade:''}}</td>
                            <td>{{grade.position}}</td>

                            <td>{{grade.grading?grade.grading.remark:''}}</td>
                        </tr>
                        </tbody>

                    </table>

                </div>

            </div>
        </div>
        <br>
    </Layout>
</template>

<script>
export default {
    name: "Report",
    props: {
        user: {},
        term:{},
        form:{},
        school:{},
        scores:{},
        total:0
    },
}
</script>

<style scoped>

</style>
