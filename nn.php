<script>
  window.onload = function() {
    document.getElementById("trainingMode").checked = true;
    document.getElementById("hdoutput").innerHTML = '<input id="toutput" placeholder="Output"></input>';
  };

  function tm() {
    trainingMode = document.getElementById("trainingMode").checked;
    document.getElementById("hdoutput").innerHTML = '<input id="toutput" placeholder="Output"></input>';
  }

  function rim() {
    trainingMode = document.getElementById("trainingMode").checked;
    document.getElementById("hdoutput").innerHTML = '';
  }

  function start() {    
    function sigmoid(x) {
      return 1 / (1 + Math.exp(-x));
    }
    
    function sigmoidDer(x) {
      return x * (1 - x);
    }

    function dot(m1, m2) {
      var result = [];
      for (var i = 0; i < m1.length; i++) {
        result[i] = [];
        for (var j = 0; j < m2[0].length; j++) {
          var sum = 0;
          for (var k = 0; k < m1[0].length; k++) {
            sum += m1[i][k] * m2[k][j];
          }
          result[i][j] = sum;
        }
      }
      return result;
    }
    
    function sub(m1, m2) {
      var result = [];
      for (var i = 0; i < m1.length; i++) {
        result[i] = [];
        for (var j = 0; j < m2[0].length; j++) {
          var sum = 0;
          for (var k = 0; k < m1[0].length; k++) {
            sum += m1[i][k] - m2[i][k];
          }
          result[i][j] = sum;
        }
      }
      return result;
    }
    
    function multi(m, n) {
      var result = [];
      for (var i = 0; i < m.length; i++) {
        result[i] = [];
        for (var j = 0; j < m[0].length; j++) {
          result[i][j] = m[i][j] * n;
        }
      }
      return result;
    }
    
    function add(m1, m2) {
      var result = [];
      for (var i = 0; i < m1.length; i++) {
        result[i] = [];
        for (var j = 0; j < m2[0].length; j++) {
          var sum = 0;
          for (var k = 0; k < m1[0].length; k++) {
            sum += m1[i][k] + m2[i][k];
          }
          result[i][j] = sum;
        }
      }
      return result;
    }
    
    function sigDot(a) {
      for(var i = 0; i < a.length; i++) {
        for(var j = 0; j < a[i].length; j++) {
          a[i][j] = sigmoid(a[i][j]);
        }
      }
      
      return a;
    }
    
    function sigmoidDerMatrix(a) {
      b = [];
      for(var i = 0; i < a.length; i++) {
        b[i] = [];
        for(var j = 0; j < a[i].length; j++) {
          b[i][j] = sigmoidDer(a[i][j]);
        }
      }
      
      return b;
    }
    
    function transpose(m) {
      var result = [];
      for (var i = 0; i < m.length; i++) {
        result[i] = [];
        for (var j = 0; j < m[0].length; j++) {
          result[j] = [];
          result[j][0] = m[i][j];
        }
      }
      return result;
    }
    
    function init_weights(x, y) {
      var result = [];
      for (var i = 0; i < x; i++) {
        result[i] = [];
        for (var j = 0; j < y; j++) {
          result[i][j] = (Math.random() * ((1 / inputSize) - (-1 / inputSize) + 1)) + (-1 / inputSize);
        }
      }
      return result;
    }
    
    function getValues(a) {
      var result = [a];
      
      return result;
    }
    
    function normalise(x, min, max) {
      return (x - min) / (max - min);
    }
    
    function denormalise(x, min, max) {
      return x * (max - min) + 1;
    }
    
    alpha = getValues(document.getElementById("alphaValue").value.split(" ").map(Number));
    hiddenSize = getValues(document.getElementById("hiddenLayerSize").value.split(" ").map(Number));
     
    input = getValues(document.getElementById("input").value.split(" ").map(Number));
    
    inputSize = document.getElementById("input").value.split(" ").map(Number).length;
    
    if(document.getElementById("trainingMode").checked === true) {
      output = getValues(document.getElementById("toutput").value.split(" ").map(Number));
      oS = document.getElementById("toutput").value.split(" ").map(Number).length;
      outputSize = oS;
    }
    
    if(document.getElementById("realInputMode").checked === true) {
      inputR = getValues(document.getElementById("input").value.split(" ").map(Number));
      outputSize = outputSize;
    }
    
    if(document.getElementById("trainingMode").checked === true) {
      weights0 = init_weights(inputSize, hiddenSize);
      weights1 = init_weights(hiddenSize, outputSize);
      
      for(var i = 0; i < 10000; i++) {
        layer0 = input;
        layer1 = sigDot(dot(layer0, weights0));
        layer2 = sigDot(dot(layer1, weights1));
        
        layer2_error = sub(layer2, transpose(output));
        layer2_delta = dot(layer2_error, sigmoidDerMatrix(layer2));
        
        layer1_error = dot(layer2_delta, transpose(weights1));
        layer1_delta = dot(transpose(layer1_error), sigmoidDerMatrix(layer1));
        
        weights1 = sub(weights1, multi(dot(transpose(layer1), layer2_delta), alpha));
        weights0 = sub(weights0, multi(dot(transpose(layer0), layer1_delta), alpha));
      }
      
      while(!(layer2[0][0] > output[0][0] - 0.01 && layer2[0][0] < output[0][0] + 0.01)) {
        weights0 = init_weights(inputSize, hiddenSize);
        weights1 = init_weights(hiddenSize, outputSize);
      
        for(var i = 0; i < 100000; i++) {
          layer0 = input;
          layer1 = sigDot(dot(layer0, weights0));
          layer2 = sigDot(dot(layer1, weights1));
          
          layer2_error = sub(layer2, transpose(output));
          layer2_delta = dot(layer2_error, sigmoidDerMatrix(layer2));
          
          layer1_error = dot(layer2_delta, transpose(weights1));
          layer1_delta = dot(transpose(layer1_error), sigmoidDerMatrix(layer1));
          
          weights1 = sub(weights1, multi(dot(transpose(layer1), layer2_delta), alpha));
          weights0 = sub(weights0, multi(dot(transpose(layer0), layer1_delta), alpha));
        }
      }
      
      alert("Done!");

    }
    
    if(document.getElementById("realInputMode").checked === true) {
      layer0 = inputR;
      layer1 = sigDot(dot(layer0, weights0));
      layer2 = sigDot(dot(layer1, weights1));
    
      htmlOutput = document.getElementById("output");
      htmlOutput.innerHTML = layer2;
      
      alert("Done!");
    }
    else {
      htmlOutput = document.getElementById("output");
      htmlOutput.innerHTML = layer2;
    }
  }
</script>

<form action="javascript:start()">
<span>Train</span>
<input id="trainingMode" type="radio" name="radio" onclick="tm()">
<br>
<span>Real Input</span>
<input id="realInputMode" type="radio" name="radio" onclick="rim()">
<br>
<br>
<input id="hiddenLayerSize" type="input" autocomplete="off" placeholder="Hidden Layer Size">
<br>
<br>
<input id="alphaValue" type="input" autocomplete="off" placeholder="Learning Rate">
<br>
<br>
<input id="input" autocomplete="off" placeholder="Input"></input>
<span id="hdoutput"></span>
<p id="output"></p>
<input type="submit"></input>
</form>