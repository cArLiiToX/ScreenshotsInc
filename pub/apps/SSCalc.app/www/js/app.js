 /**
  * Calculate order cost
  * @return {[type]} [description]
  */
 $('#calculateCosts').click(function() {
  var standardQuantity      = Number($('#quantity').val()),
      xlQuantity            = Number($('#xlQuantity').val()),
      xxlQuantity           = Number($('#xxlQuantity').val()),
      standardQuantityTotal = 0,
      xlTotal               = 0,
      xxlTotal              = 0,
      prePurchaseStatus     = $('#prePurchasedOption').val(),
      baseLevelOption       = $('#baseLevelOption').val (),
      baseLevelPrice        = 0,
      printQuantity         = (standardQuantity + xlQuantity + xxlQuantity),
      printZones            = 1,
      zone1price            = 0,
      zone2price            = 0,
      zone3price            = 0,
      zone4price            = 0,
      printZone1Colors      = Number($('#printArea1').val()),
      printZone2Colors      = Number($('#printArea2').val()),
      printZone3Colors      = Number($('#printArea3').val()),
      printZone4Colors      = Number($('#printArea4').val()),
      personalizations      = Number($('#personalizations').val()),
      personalizationCost   = 4,
      personalizationTotal  = personalizations * personalizationCost,
      oneColorNumbers       = Number($('#oneColorNumbers').val()),
      oneColorNumberCost    = 2,
      oneColorNumberTotal   = oneColorNumbers * oneColorNumberCost,
      twoColorNumbers       = Number($('#twoColorNumbers').val()),
      twoColorNumberCost    = 4,
      twoColorNumberTotal   = twoColorNumbers * twoColorNumberCost,
      colorChanges          = Number($('#colorChanges').val()),
      colorChangeCost       = 5,
      colorChangeTotal      = colorChanges * colorChangeCost,
      transfers             = Number($('#transfers').val()),
      transferTotal         = transfers * .25,
      screenQuantity        = 0,
      screenCharge          = 5,
      screenTotal           = 0,
      retailCharge          = 0,
      retailTotal           = 0,
      costPerItem           = 0,
      retailCostPerItem     = 0,
      wholesalePrice        = 0,
      retailPrice           = 0;

  /**
   * Assign baseLevelPrice according to selected base level range
   */
  switch(baseLevelOption) {
    case '1':
      baseLevelPrice = 5.00;
      break;
    case '2':
      baseLevelPrice = 6.50;
      break;
    case '3':
      baseLevelPrice = 8.00;
      break;
    case '4':
      baseLevelPrice = 9.50;
      break;
    case '5':
      baseLevelPrice = 11.00;
      break;
    case '6':
      baseLevelPrice = 12.50;
      break;
    case '7':
      baseLevelPrice = 14.00;
      break;
    case '8':
      baseLevelPrice = 16.00;
      break;
    case '9':
      baseLevelPrice = 19.50;
      break;
    case '10':
      baseLevelPrice = 22.00;
      break;
  }

  /**
   * Assign color costs (base and additional) based on printQuantity
   * Assign retail charge based on retail charge table
   */
  if (printQuantity <= 23) {
    baseColorCost = 2.90;
    additionalColorCost = 1;
    retailCharge = 4.10;
  } else if (printQuantity >= 24 && printQuantity <= 35) {
    baseColorCost = 2.30;
    additionalColorCost = 0.75;
    retailCharge = 3.70;
  } else if (printQuantity >= 36 && printQuantity <= 47) {
    baseColorCost = 1.70;
    additionalColorCost = 0.50;
    retailCharge = 3.30;
  } else if (printQuantity >= 48 && printQuantity <= 71) {
    baseColorCost = 1.50;
    additionalColorCost = 0.25;
    retailCharge = 2.50;
  } else if (printQuantity >= 72 && printQuantity <= 143) {
    baseColorCost = 1.20;
    additionalColorCost = 0.15;
    retailCharge = 1.80;
  } else if (printQuantity >= 144 && printQuantity <= 199) {
    baseColorCost = 1.05;
    additionalColorCost = 0.10;
    retailCharge = 0.95;
  } else if (printQuantity >= 200 && printQuantity <= 299) {
    baseColorCost = 1.05;
    retailCharge = .85;
  } else if (printQuantity >= 300 && printQuantity <= 499) {
    baseColorCost = 0.95;
    additionalColorCost = 0.05;
    retailCharge = 0.75;
  } else if (printQuantity >= 500 && printQuantity <= 749) {
    baseColorCost = 0.75;
    retailCharge = 0.65;
  } else if (printQuantity >= 750 && printQuantity <= 999) {
    baseColorCost = 0.70;
    retailCharge = 0.55;
  } else if (printQuantity >= 1000 && printQuantity <= 1999) {
    baseColorCost = 0.65;
    retailCharge = 0.45;
  } else if (printQuantity >= 2000 && printQuantity <= 2999) {
    baseColorCost = 0.62;
    retailCharge = 0.40;
  } else if (printQuantity >= 3000) {
    baseColorCost = 0.58;
    retailCharge = 0.40;
  }

  /**
   * Calculate print zone totals
   */
  zone1Total        = printZone1Colors >= 2 ?
                      baseColorCost + ((printZone1Colors - 1) * additionalColorCost) :
                      baseColorCost;
  zone2Total        = printZone2Colors >= 1 ?printZone2Colors >= 2 ?
                      baseColorCost + ((printZone2Colors - 1) * additionalColorCost) :
                      baseColorCost : 0;
  zone3Total        = printZone3Colors >= 1 ? printZone3Colors >= 2 ?
                      baseColorCost + ((printZone3Colors - 1) * additionalColorCost) :
                      baseColorCost : 0;
  zone4Total        = printZone4Colors >= 1 ? printZone4Colors >= 2 ?
                      baseColorCost + ((printZone4Colors - 1) * additionalColorCost) :
                      baseColorCost : 0;

  /**
   * Calculate quantity totals
   */
  standardQuantityTotal = (standardQuantity * baseLevelPrice);
  xlQuantityTotal       = (xlQuantity * 1.5) + (xlQuantity * baseLevelPrice);
  xxlQuantityTotal      = (xxlQuantity * 2.5) + (xxlQuantity * baseLevelPrice);
  retailTotal           = printQuantity * retailCharge;

  numberOfPrintColors   = Number(printZone1Colors) + Number(printZone2Colors) +
                          Number(printZone3Colors) + Number(printZone4Colors);

  printPrice            = zone1Total + zone2Total + zone3Total + zone4Total;

  productTotal          = (standardQuantityTotal + xlQuantityTotal + xxlQuantityTotal);
  screenTotal           = numberOfPrintColors * screenCharge;

  /**
   * Calculate Final Costs
   */
  wholesalePrice    = prePurchaseStatus == 0 ?
                      productTotal +
                      oneColorNumberTotal + twoColorNumberTotal +
                      colorChangeTotal + transferTotal +
                      personalizationTotal +
                      (printPrice * printQuantity) + screenTotal :
                      oneColorNumberTotal + twoColorNumberTotal +
                      colorChangeTotal + transferTotal +
                      personalizationTotal +
                      (printPrice * printQuantity) + screenTotal;

  retailPrice       = prePurchaseStatus == 0 ?
                      wholesalePrice + retailTotal :
                      wholesalePrice + retailTotal + productTotal;

  costPerItem       = wholesalePrice / printQuantity;
  retailCostPerItem = retailPrice / printQuantity;

  displayResults(wholesalePrice, retailPrice, costPerItem, retailCostPerItem);
});

/**
 * Update view with the calculated totals
 * @var [type]
 */
displayResults = function(wholesalePrice, retailPrice, costPerItem, retailCostPerItem) {
  var wholesalePrice    = wholesalePrice.toFixed(2),
      retailPrice       = retailPrice.toFixed(2),
      costPerItem       = costPerItem.toFixed(2),
      retailCostPerItem = retailCostPerItem.toFixed(2);

  $('.wholesalePriceDisplay').html(wholesalePrice);
  $('.retailPriceDisplay').html(retailPrice);
  $('.wholesaleCostPerItemDisplay').html(costPerItem);
  $('.retailCostPerItemDisplay').html(retailCostPerItem);
};
