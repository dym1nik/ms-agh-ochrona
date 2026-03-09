import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static values = {
    message: String,
  };

  submit(event) {
    const msg = this.messageValue || "Na pewno?";
    if (!window.confirm(msg)) {
      event.preventDefault();
      event.stopPropagation();
    }
  }
}