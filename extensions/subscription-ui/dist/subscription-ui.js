(() => {
  var __async = (__this, __arguments, generator) => {
    return new Promise((resolve, reject) => {
      var fulfilled = (value) => {
        try {
          step(generator.next(value));
        } catch (e) {
          reject(e);
        }
      };
      var rejected = (value) => {
        try {
          step(generator.throw(value));
        } catch (e) {
          reject(e);
        }
      };
      var step = (x) => x.done ? resolve(x.value) : Promise.resolve(x.value).then(fulfilled, rejected);
      step((generator = generator.apply(__this, __arguments)).next());
    });
  };

  // node_modules/@shopify/admin-ui-extensions/build/esm/api.mjs
  function extend(extensionPoint, callback) {
    return self.shopify.extend(extensionPoint, callback);
  }

  // node_modules/@remote-ui/core/build/esm/component.mjs
  function createRemoteComponent(componentType) {
    return componentType;
  }

  // node_modules/@shopify/admin-ui-extensions/build/esm/components/BlockStack/BlockStack.mjs
  var BlockStack = createRemoteComponent("BlockStack");

  // node_modules/@shopify/admin-ui-extensions/build/esm/components/Checkbox/Checkbox.mjs
  var Checkbox = createRemoteComponent("Checkbox");

  // node_modules/@shopify/admin-ui-extensions/build/esm/components/Text/Text.mjs
  var Text = createRemoteComponent("Text");

  // extensions/subscription-ui/src/index.ts
  extend("Admin::Product::SubscriptionPlan::Add", Add);
  extend("Admin::Product::SubscriptionPlan::Create", Create);
  extend("Admin::Product::SubscriptionPlan::Remove", Remove);
  extend("Admin::Product::SubscriptionPlan::Edit", Edit);
  function Edit(root, { extensionPoint }) {
    root.appendChild(
      root.createComponent(
        Text,
        {},
        `Selling plan options are coming soon. Go to the Blackcart app to modify your existing selling plan`
      )
    );
    root.mount();
  }
  function Create(root, { extensionPoint }) {
    root.appendChild(
      root.createComponent(
        Text,
        {},
        `Selling plan options are coming soon. Go to the Blackcart app to modify your existing selling plan`
      )
    );
    root.mount();
  }
  var backend = "https://shop-app.blackcart.com";
  function fetchProgramId(sessionToken) {
    return __async(this, null, function* () {
      const token = yield sessionToken.getSessionToken();
      const response = yield fetch(backend + "/api/stores/programs", {
        headers: {
          Authorization: "Bearer " + token || "unknown token"
        }
      });
      const responseData = yield response.json();
      return responseData.programs[0].id;
    });
  }
  function fetchVariants(sessionToken, productId, variantId) {
    return __async(this, null, function* () {
      const token = yield sessionToken.getSessionToken();
      const response = yield fetch(backend + "/api/stores/products/" + productId, {
        headers: {
          Authorization: "Bearer " + token || "unknown token"
        }
      });
      const responseData = yield response.json();
      if (variantId !== void 0) {
        const filteredVariants = responseData.data.product.variants.edges.filter(
          (variant) => variant.node.id === variantId
        );
        responseData.data.product.variants.edges = filteredVariants;
      }
      return responseData;
    });
  }
  function variantsInSellingPlan(sessionToken, variantIds, programId) {
    return __async(this, null, function* () {
      const token = yield sessionToken.getSessionToken();
      const encodedVariantIds = variantIds.map((id) => encodeURIComponent(id)).join("&");
      const url = `${backend}/api/stores/programs/${programId}/variants?shopify_variant_ids=${encodedVariantIds}`;
      const response = yield fetch(url, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: "Bearer " + token || "unknown token"
        }
      });
      const responseData = yield response.json();
      return responseData;
    });
  }
  var selectedVariants = {};
  var program = "";
  function Add(root, api) {
    const { close, done, setPrimaryAction, setSecondaryAction } = api.container;
    setPrimaryAction({
      content: "Add to plan",
      onAction: () => __async(this, null, function* () {
        const token = yield sessionToken.getSessionToken();
        const selectedVariantIds = Object.keys(selectedVariants);
        const formData = {
          productId: api.data.productId,
          selected_variant_ids: selectedVariantIds
        };
        const response = yield fetch(backend + "/api/stores/programs/" + program + "/variants", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: "Bearer " + token || "unknown token"
          },
          body: JSON.stringify(formData)
        });
        done();
      })
    });
    setSecondaryAction({
      content: "Cancel",
      onAction: () => close()
    });
    const sessionToken = api.sessionToken;
    const parts = api.data.productId.split("/");
    const productId = parts[parts.length - 1];
    const variantId = api.data.variantId;
    fetchProgramId(sessionToken).then((programId) => {
      program = programId;
      fetchVariants(sessionToken, productId, variantId).then((responseData) => {
        const variants = responseData.data.product.variants.edges;
        const variantIds = variants.map((variant) => variant.node.id);
        variantsInSellingPlan(sessionToken, variantIds, program).then((variantsInSellingPlanData) => {
          const filteredVariants = variants.filter((variant) => {
            const variantId2 = variant.node.id.split("/").pop();
            const sellingPlanKey = `productVariant${variantId2}`;
            return !variantsInSellingPlanData.data.sellingPlanGroup[sellingPlanKey];
          });
          const blockStack = root.createComponent(BlockStack, {
            inlineAlignment: "leading",
            spacing: "tight"
          });
          const addText = variantId !== void 0 ? "Add variant to TBYB" : "Add product and variants to TBYB";
          const allVariantsAdded = variantId !== void 0 ? "Variant already added to TBYB" : "All variants are already added to TBYB";
          if (filteredVariants === void 0 || filteredVariants.length == 0) {
            blockStack.appendChild(root.createComponent(Text, {}, allVariantsAdded));
          } else {
            blockStack.appendChild(root.createComponent(Text, {}, addText));
          }
          const handleCheckboxChange = (variantId2, variantName, isChecked, checkbox) => {
            checkbox.updateProps({ checked: isChecked });
            if (isChecked) {
              selectedVariants[variantId2] = variantName;
            } else {
              delete selectedVariants[variantId2];
            }
          };
          filteredVariants.forEach((variant) => {
            const variantName = variant.node.displayName;
            const variantId2 = variant.node.id;
            const checkbox = root.createComponent(Checkbox, {
              label: `${variantName}`,
              onChange: (isChecked) => handleCheckboxChange(variantId2, variantName, isChecked, checkbox)
            });
            blockStack.appendChild(checkbox);
          });
          root.appendChild(blockStack);
          root.mount();
        }).catch((error) => {
          console.error("Error fetching data:", error);
        });
      }).catch((error) => {
        console.error("Error fetching data:", error);
      });
    });
  }
  function Remove(root, api) {
    const { close, done, setPrimaryAction, setSecondaryAction } = api.container;
    setPrimaryAction({
      content: "Remove from plan",
      onAction: () => __async(this, null, function* () {
        const token = yield sessionToken.getSessionToken();
        const selectedVariantIds = Object.keys(selectedVariants);
        const formData = {
          productId: api.data.productId,
          selected_variant_ids: selectedVariantIds
        };
        const response = yield fetch(backend + "/api/stores/programs/" + program + "/variants", {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
            Authorization: "Bearer " + token || "unknown token"
          },
          body: JSON.stringify(formData)
        });
        done();
      })
    });
    setSecondaryAction({
      content: "Cancel",
      onAction: () => close()
    });
    const sessionToken = api.sessionToken;
    const parts = api.data.productId.split("/");
    const productId = parts[parts.length - 1];
    const variantId = api.data.variantId;
    fetchProgramId(sessionToken).then((programId) => {
      program = programId;
      fetchVariants(sessionToken, productId, variantId).then((responseData) => {
        const variants = responseData.data.product.variants.edges;
        const variantIds = variants.map((variant) => variant.node.id);
        variantsInSellingPlan(sessionToken, variantIds, program).then((variantsInSellingPlanData) => {
          const filteredVariants = variants.filter((variant) => {
            const variantId2 = variant.node.id.split("/").pop();
            const sellingPlanKey = `productVariant${variantId2}`;
            return variantsInSellingPlanData.data.sellingPlanGroup[sellingPlanKey];
          });
          const blockStack = root.createComponent(BlockStack, {
            inlineAlignment: "leading",
            spacing: "tight"
          });
          const removeText = variantId !== void 0 ? "Remove variant from TBYB" : "Remove variants from TBYB";
          blockStack.appendChild(root.createComponent(Text, {}, removeText));
          const handleCheckboxChange = (variantId2, variantName, isChecked, checkbox) => {
            checkbox.updateProps({ checked: isChecked });
            if (isChecked) {
              selectedVariants[variantId2] = variantName;
            } else {
              delete selectedVariants[variantId2];
            }
          };
          filteredVariants.forEach((variant) => {
            const variantName = variant.node.displayName;
            const variantId2 = variant.node.id;
            const checkbox = root.createComponent(Checkbox, {
              label: `${variantName}`,
              onChange: (isChecked) => handleCheckboxChange(variantId2, variantName, isChecked, checkbox)
            });
            blockStack.appendChild(checkbox);
          });
          root.appendChild(blockStack);
          root.mount();
        }).catch((error) => {
          console.error("Error fetching data:", error);
        });
      }).catch((error) => {
        console.error("Error fetching data:", error);
      });
    });
  }
})();
//# sourceMappingURL=subscription-ui.js.map
