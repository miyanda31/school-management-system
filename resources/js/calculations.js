export class Calculations {
   constructor(currentMonth,loan) {
        this.currentMonth = currentMonth
        this.loan = loan
   }

   isMonthlyDefault() {
       return Number(this.currentMonth.status) === 4
   }
   isFirstDefault() {
       return Number(this.currentMonth.status) === 8
   }
   isSecondDefault() {
       return Number(this.currentMonth.status) === 12
   }
    isFullDefaultPayment() {
       return Number(this.currentMonth.status) === 11
   }
    isNormalMonth() {
        return Number(this.currentMonth.status) !== 4 && Number(this.currentMonth.status) !== 12 && Number(this.currentMonth.status) !== 8 && Number(this.currentMonth.status) !== 7 && Number(this.currentMonth.status) !== 11
    }
    normalInterest() {

        var balance = 0;

         if (parseInt(this.currentMonth.status) === 5) {
            balance = 0
        }
        else if (parseInt(this.currentMonth.status) === 4) {
            balance = Number(this.loan.interest.rate) /100 * (Number(this.currentMonth.ending_balance) - (10/100*Number(this.loan.repayment)))
        }
        else {
            balance = Number(this.loan.interest.rate) /100 * Number(this.currentMonth.ending_balance)
        }
        return balance;
    }

    normalEndingBalance() {
        var balance = 0;
            if (Number(this.currentMonth.ending_balance) > Number(this.currentMonth.beginning_balance)) {
                var interest = Number(this.loan.interest.rate) /100 * (Number(this.currentMonth.ending_balance) - (10/100*Number(this.loan.repayment)))

                balance = Number(this.currentMonth.ending_balance) + interest

            }
            else {
                balance = Number(this.currentMonth.ending_balance ) + Number(this.loan.interest.rate)/100*Number(this.currentMonth.ending_balance )
            }
        return balance;
    }
    defaultPaymentEndingBalance() {
        var balance;
        if (this.currentMonth !== null) {
            if (Number(this.currentMonth.ending_balance) > Number(this.currentMonth.beginning_balance)) {
                var interest = Number(this.loan.interest.rate) /100 * (Number(this.currentMonth.ending_balance)  - (10/100*Number(this.loan.repayment)))

                balance = Number(this.currentMonth.ending_balance) + interest - (Number(this.loan.interest.rate) /100 *(10/100*Number(this.loan.repayment)))

            }
            else {
                balance = Number(this.currentMonth.ending_balance ) + Number(this.loan.interest.rate/100*Number(this.currentMonth.ending_balance )) - (Number(this.loan.interest.rate) /100 *(10/100*Number(this.loan.repayment)))
            }
        }
        else {
            balance = Number(this.currentMonth.beginning_balance) + Number(this.loan.interest.rate) /100 * Number(this.currentMonth.beginning_balance)
        }
        return balance;
    }

    firstDefaultBalance() {
            return Number(this.currentMonth.beginning_balance)+ this.loan.interest.rate/100 * Number(this.currentMonth.beginning_balance);
    }
    secondDefaultBalance() {
        return Number(this.currentMonth.beginning_balance);
    }
    loanStatus(status) {
        var s = 'Not Settled'
        var v = Number(status)?Number(status):parseInt(this.currentMonth.status)
        switch (v) {
            case 1:
                s = 'Settled'
                break
            case 2:
                s = 'Partial'
                break
            case 3:
                s = 'Defaulted'
                break
            case 4:
                s = 'Settled'
                break
            case 5:
                s = 'Paid Off'
                break
            case 6:
                s = 'Early Payment'
                break
            case 7:
                s = 'Full Payment'
                break
            case 8:
            case 12:
                s = 'Full Default'
                break
            case 11:
                s = 'F Def Pay'
                break
            case 10:
                s = 'Prepayment'
                break
            case 14:
                s = 'Paid Off'
                break
            case 15:
                s = 'NICO settled'
                break

        }

        return s;

    }
}
