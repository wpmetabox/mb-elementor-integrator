const MbSelect = () => {
  return {
    onReady() {
      if (this.$el.hasClass("elementor-control-subfield")) {
        this.subfield = this.$el.find("select");
        this.subfield.change(() => {
          let value = this.subfield.val();
          if ("" !== value) {
            value = value.split(":")[1];
          }
          // console.log("SELECT 2", this);

          //Show / Hide Text Link
          const block_link = this._parent.$el.find(
            ".elementor-control-display_text_for_link"
          );
          //set value default
          if (
            undefined !== this.subfield.data("link") &&
            "" !== this.subfield.data("link")
          ) {
            block_link
              .find('input[data-setting="display_text_for_link"]')
              .val(this.subfield.data("link"));
          }
          if (value.indexOf("link") > -1 || value.indexOf("url") > -1) {
            block_link.show();
          } else {
            block_link.hide();
          }
        });
      }
    },
  };
};
export default MbSelect;
