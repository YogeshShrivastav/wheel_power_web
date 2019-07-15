import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-add-lead',
  templateUrl: './add-lead.component.html',
  animations: [slideToTop()]

})
export class AddLeadComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
