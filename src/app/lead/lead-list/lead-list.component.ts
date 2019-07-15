import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-lead-list',
  templateUrl: './lead-list.component.html',
  animations: [slideToTop()]


})
export class LeadListComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
