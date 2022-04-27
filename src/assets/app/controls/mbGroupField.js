const Mbgroupfield = () => {
  return {
    onReady() {
      const checkParent = setInterval((self = this) => {
        if (self._parent != undefined) {
          clearInterval(checkParent);

          //Init control sub field
          self.control_repeater = self.options.elementSettingsModel.attributes[
            "map-field-group"
          ]
            ? self.options.elementSettingsModel.attributes["map-field-group"]
            : undefined;

          var subfield_Value = [];
          if (self.control_repeater.models.length > 0) {
            self.control_repeater.models.map((value) => {
              subfield_Value.push(value.attributes);
            });
          }
          self.control_select = self.$el.find(".group-field-select");

          //   console.log(
          //     "REPEATER: ",
          //     self._parent.$el.find(".elementor-control-map-field-group")
          //   );

          self.control_map_content = self._parent.$el.find(
            ".elementor-control-map-field-group .elementor-repeater-fields-wrapper"
          );
          self.control_map_button = self._parent.$el
            .find(".elementor-control-map-field-group .elementor-repeater-add")
            .hide();
          self.control_map_select = self._parent.$el.find(
            '.elementor-control-map-field-group select[data-setting="subfield"]'
          );

          //   console.log(self.control_map_select);

          //   console.log("VALUES: ", subfield_Value);
          //   console.log(subfield_Value.models[0].attributes["subfield"]);
          self.loadSubField(subfield_Value);
          //   self.control_map_select.on("change", self.changeSubFied());
          self.control_select.change(() => self.saveValue());
          //   self.control_map_select.change(() => self.changeSubFied());
          //   self.control_repeater.sort(() => self.saveValue());
        }
      }, 300);
    },
    saveValue() {
      this.setValue(this.control_select.val());
      //Load Sub Field
      this.loadSubField();
    },
    loadSubField(defaultValue = undefined) {
      if ("" === this.control_select.val()) {
        //   this.control_map_select.html("");
        this.control_repeater.reset();
      } else {
        const self = this;
        if (undefined !== self._parent.$el && "" !== self._parent.$el) {
          jQuery.ajax({
            url: mebi_ajax.url,
            type: "post",
            data: {
              action: "group_subfield",
              nonce: mebi_ajax.nonce,
              groupfield: this.control_select.val(),
            },
            success: (res) => {
              if (true === res.success) {
                if (undefined !== self._parent) {
                  self.control_repeater.reset();

                  var list_fields = [];

                  jQuery.each(res.data, (value, key) => {
                    self.control_map_button.trigger("click");
                    list_fields.push(
                      `<option value="${value}">${key}</option>`
                    );
                  });

                  const map_selects = self._parent.$el.find(
                    '.elementor-control-map-field-group select[data-setting="subfield"]'
                  );
                  jQuery(map_selects).html(list_fields.join(""));

                  jQuery.each(map_selects, (index, map_select) => {
                    if (
                      undefined !== defaultValue &&
                      "" !== defaultValue[index]["subfield"]
                    ) {
                      jQuery(map_select)
                        .attr(
                          "data-link",
                          defaultValue[index]["display_text_for_link"]
                        )
                        .find(
                          "option[value='" +
                            defaultValue[index]["subfield"] +
                            "']"
                        )
                        .attr("selected", true)
                        .trigger("change");
                    } else {
                      jQuery(map_select)
                        .find("option:eq(" + index + ")")
                        .attr("selected", true)
                        .trigger("change");
                    }
                  });
                }
              }
            },
          });
        }
      }
    },
    onBeforeDestroy() {
      this.saveValue();
      this.loadSubField();
    },
  };
};

export default Mbgroupfield;
