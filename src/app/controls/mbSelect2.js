const MbSelect2 = () => {
  return {
    onReady() {
      if (this.$el.hasClass("elementor-control-map-field-group")) {
        console.log("RRR: ", this);
      }
    },
    onBeforeDestroy() {
      this.saveValue();
    },
  };
};
export default MbSelect2;
