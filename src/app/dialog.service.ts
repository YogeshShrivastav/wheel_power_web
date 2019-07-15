import { Injectable } from '@angular/core';
import Swal from 'sweetalert2';

// CommonJS
// const Swal = require('sweetalert2')
@Injectable({
  providedIn: 'root'
})
export class DialogService {

  constructor() { }

  success(title:any,msg:any)
  {
    Swal.fire(
      title+'!',
      msg+'.',
      'success'
      
      )
  }

  delete(msg:any)
  {
    return Swal.fire({
      title: 'Are you sure?',
      text: 'You will not be able to recover this '+msg,
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'No, keep it'
    }).then((result) => {
      if (result.value) {
        return true;
        // For more information about handling dismissals please visit
        // https://sweetalert2.github.io/#handling-dismissals
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire(
          'Cancelled',
          'Your '+ msg +' is safe &#x263A;',
          'error'
          )
          return false;
          
        }
      })
    }

  confirm(msg:any)
  {
    return Swal.fire({
      title: 'Are you sure?',
      text: 'You want to Dispatch this '+msg,
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    }).then((result) => {
      if (result.value) {
        return true;
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire(
          'Cancelled',
          'Your '+ msg +' is not dispatch &#x263A;',
          'error'
          )
          return false;
          
        }
      })
    }

    error(msg:any)
    {
      Swal.fire({
        type: 'error',
        title: 'Oops...',
        text: msg,
        // footer: '<a href>Why do I have this issue?</a>'
      })
    }

}