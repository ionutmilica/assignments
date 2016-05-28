package assignment1;

import java.awt.*;

public class Color3 {

    protected int r;
    protected int g;
    protected int b;
    protected int a;

    public Color3(int c) {
        Color color = new Color(c);

        this.r = color.getRed();
        this.g = color.getGreen();
        this.b = color.getBlue();
        this.a = color.getAlpha();
        clamp();
    }

    public Color3(int r, int g, int b, int a) {
        this.r = r;
        this.g = g;
        this.b = b;
        this.a = a;
        clamp();
    }

    public Color3(int r, int g, int b) {
        this.r = r;
        this.g = g;
        this.b = b;
        this.a = 1;
        clamp();
    }

    public Color3(float r, float g, float b) {
        this((int) (r * 255 + 0.5), (int) (g * 255 + 0.5), (int) (b * 255 + 0.5));
    }

    public Color3(float r, float g, float b, float a) {
        this((int) (r * 255 + 0.5), (int) (g * 255 + 0.5), (int) (b * 255 + 0.5), (int) (a * 255 + 0.5));
    }

    public int getRed() {
        return r;
    }

    public int getGreen() {
        return g;
    }

    public int getBlue() {
        return b;
    }

    public int getAlpha() { return a; }

    public Color3 add(Color3 o) {
        return new Color3(r + o.getRed(), g + o.getGreen(), b + o.getBlue(), a + o.getAlpha());
    }

    public Color3 add(int val) {
        return new Color3(r + val, g + val, b + val, a + val);
    }

    public Color3 sub(Color3 o) {
        return new Color3(r - o.getRed(), g - o.getGreen(), b - o.getBlue(), a - o.getAlpha());
    }

    public Color3 mul(double d) {
        return new Color3((int) (d * r), (int) (d * g), (int) (d * b), (int) (a * b));
    }

    public Color3 div(double d) {
        return new Color3((int) (r / d), (int) (g / d), (int) (b / d), (int) (a / d));
    }

    public int diff(Color3 o) {
        return Math.abs(r - o.getRed()) +  Math.abs(g - o.getGreen()) +  Math.abs(b - o.getBlue());
    }

    public Color toColor() {
        return new Color(clamp(r), clamp(g), clamp(b), clamp(a));
    }

    public int getRGB() {
        return toColor().getRGB();
    }

    protected int clamp(int c) {
        return Math.max(0, Math.min(255, c));
    }

    protected void clamp() {
        r = clamp(r);
        g = clamp(g);
        b = clamp(b);
        a = clamp(a);
    }

    public String toString() {
        return "rgba(" + r + ", " + g + ", " + b + ", " + a + ")";
    }
}
