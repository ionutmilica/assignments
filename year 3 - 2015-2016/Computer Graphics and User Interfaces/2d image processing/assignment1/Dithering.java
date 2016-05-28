package assignment1;

import java.awt.image.BufferedImage;

public class Dithering implements IFilter {

    Color3[] palette = new Color3[] {
            new Color3(  0,   0,   0),
            new Color3(  0,   0, 255),
            new Color3(  0, 255,   0),
            new Color3(  0, 255, 255),
            new Color3(255,   0,   0),
            new Color3(255,   0, 255),
            new Color3(255, 255,   0),
            new Color3(255, 255, 255)
    };

    protected Color3 findClosestPaletteColor(Color3 color) {
        Color3 closest = palette[0];

        for (Color3 n : palette)
            if (n.diff(color) < closest.diff(color))
                closest = n;

        return closest;
    }

    protected Color3[][] transform(BufferedImage img) {
        Color3[][] newImg = new Color3[img.getWidth()][img.getHeight()];

        for (int y = 0; y < img.getHeight(); y++) {
            for (int x = 0; x < img.getWidth(); x++) {
                newImg[x][y] = new Color3(img.getRGB(x, y));
            }
        }

        return newImg;
    }

    /**
     * Apply Floyd–Steinberg Dithering algorithm
     */
    public BufferedImage apply(BufferedImage source) {
        BufferedImage dest = new BufferedImage(source.getWidth(), source.getHeight(), BufferedImage.TYPE_INT_RGB);
        Color3[][] newImg = transform(source);

        int w = source.getWidth();
        int h = source.getHeight();

        for (int y = 0; y < source.getHeight(); y++) {
            for (int x = 0; x < source.getWidth(); x++) {
                Color3 oldpixel =  newImg[x][y];
                Color3 newpixel  = findClosestPaletteColor(oldpixel);

                dest.setRGB(x, y, newpixel.toColor().getRGB());

                Color3 err = oldpixel.sub(newpixel);

                if (x + 1 < w) {
                    newImg[x + 1][y] = newImg[x + 1][y].add(err.mul(7 / 16.0));
                }
                if (x - 1 >= 0 && y + 1 < h) {
                    newImg[x - 1][y + 1] = newImg[x - 1][y + 1].add(err.mul(3 / 16.0));
                }
                if (y+1 < h) {
                    newImg[x][y + 1] = newImg[x  ][y+1].add(err.mul(5 / 16.0));
                }
                if (x + 1 < w && y + 1 < h) {
                    newImg[x + 1][y + 1] = newImg[x + 1][y + 1].add(err.mul(1 / 16.0));
                }
            }
        }

        return dest;
    }
}
