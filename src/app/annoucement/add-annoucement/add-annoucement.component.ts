import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-add-annoucement',
  templateUrl: './add-annoucement.component.html',
  animations: [slideToTop()]
})
export class AddAnnoucementComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
