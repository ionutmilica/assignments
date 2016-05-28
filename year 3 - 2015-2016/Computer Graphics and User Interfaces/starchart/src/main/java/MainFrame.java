import com.jogamp.opengl.GL;
import com.jogamp.opengl.GL2;
import com.jogamp.opengl.GLAutoDrawable;
import com.jogamp.opengl.GLCapabilities;
import com.jogamp.opengl.GLEventListener;
import com.jogamp.opengl.GLProfile;
import com.jogamp.opengl.awt.GLCanvas;
import com.jogamp.opengl.fixedfunc.GLMatrixFunc;
import com.jogamp.opengl.glu.GLU;
import com.jogamp.opengl.util.Animator;
import javax.swing.JFrame;
import astro.*;
import com.jogamp.opengl.util.gl2.GLUT;
import textures.TextureHandler;

import java.util.ArrayList;
import java.util.HashMap;

public class MainFrame extends JFrame implements GLEventListener
{
    private GLCanvas canvas;
    private Animator animator;
    private double v_size = 1.0;

    // GLU object used for mipmapping.
    private GLU glu;
    private GLUT glut;

    public static void main(String args[])
    {
        new MainFrame();
    }

    public MainFrame()
    {
        super("OpenGL");
        this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        this.setSize(1000, 1000);
        this.initializeJogl();
        this.setVisible(true);
    }

    private void initializeJogl()
    {
        GLProfile glprofile = GLProfile.getDefault();
        GLCapabilities capabilities = new GLCapabilities(glprofile);

        capabilities.setHardwareAccelerated(true);
        capabilities.setDoubleBuffered(true);
        capabilities.setNumSamples(2);
        capabilities.setSampleBuffers(true);

        this.canvas = new GLCanvas(capabilities);
        this.getContentPane().add(this.canvas);
        this.canvas.addGLEventListener(this);
        this.animator = new Animator(this.canvas);
        this.animator.start();
    }

    // Polul sud
    //PolarProjectionMap ppm = new PolarProjectionMap(10.125001, -80.018519);
    // Tecuci
    PolarProjectionMap ppm = new PolarProjectionMap(27.424127, 45.849092);
    // Londra
    //PolarProjectionMap ppm = new PolarProjectionMap(-0.127758, 51.507351);

    boolean disableConstellationLines = false;
    boolean disableMessier = false;

    private int ppm_list;

    HashMap<String, TextureHandler> textures = new HashMap<String, TextureHandler>();

    public void init(GLAutoDrawable canvas)
    {
        GL2 gl = canvas.getGL().getGL2();
        gl.glClearColor(0, 0, 0, 0);
        gl.glMatrixMode(GLMatrixFunc.GL_MODELVIEW);
        glut = new GLUT();

        // Create a new GLU object.
        glu = GLU.createGLU();

        if ( ! disableMessier) { /*
            gl.glGenTextures(NO_TEXTURES, texture, 0);
            textures.put("m1", new TextureHandler(gl, glu, "images/m1.jpg", true, texture)); */
        }

        for (int i = 1; i <= 110; i++) {
            textures.put("m"+i, new TextureHandler(gl, glu, "images/m"+i+".jpg", true));
        }

        ppm.setFileSep(",");
        ppm.initializeConstellationStars("data/beyer.dat");
        ppm.initializeConstellationBoundaries("data/cbounds.dat");
        ppm.initializeConstellationLines("data/conlines.dat");
        ppm.initializeConstellationNames("data/cnames.dat");
        ppm.initializeMessierObjects("data/messier.dat");

        this.ppm_list = gl.glGenLists(1);
        gl.glNewList(this.ppm_list, GL2.GL_COMPILE);
            this.makePPM(gl);
        gl.glEndList();
    }

    void makePPM(GL2 gl) {

        /** Draw constellation lines **/
        gl.glColor3f(0.0f, 1.0f, 0.0f);
        gl.glBegin(GL.GL_LINES);
        for (PolarProjectionMap.ConstellationBoundaryLine line : ppm.getConBoundaryLines()) {
            if (line.isVisible()) {
                gl.glVertex2f((float) line.getPosX1(), (float) line.getPosY1());
                gl.glVertex2f((float) line.getPosX2(), (float) line.getPosY2());
            }
        }
        gl.glEnd();


        if (!disableConstellationLines) {
            gl.glColor3f(1.0f, 1.0f, 0.0f);
            gl.glBegin(GL.GL_LINES);
            for (PolarProjectionMap.ConstellationLine line : ppm.getConLines()) {
                if (line.isVisible()) {
                    gl.glVertex2f((float) line.getPosX1(), (float) line.getPosY1());
                    gl.glVertex2f((float) line.getPosX2(), (float) line.getPosY2());
                }
            }
            gl.glEnd();
        }
        /** Draw stars **/
        gl.glColor3f(1.0f, 1.0f, 1.0f);
        gl.glPointSize(1f);
        gl.glBegin(GL.GL_POINTS);
        for (PolarProjectionMap.ConstellationStar star : ppm.getConStars()) {
            if (star.isVisible()) {
                float x = (float) star.getPosX(), y = (float) star.getPosY();
                gl.glVertex2f((float) x, (float) y);
            }
        }

        gl.glEnd();

        gl.glRasterPos2d(ppm.getNorthP().getPosX(), ppm.getNorthP().getPosY());
        glut.glutBitmapString(GLUT.BITMAP_HELVETICA_10, "N");

        gl.glRasterPos2d(ppm.getSouthP().getPosX(), ppm.getSouthP().getPosY());
        glut.glutBitmapString(GLUT.BITMAP_HELVETICA_10, "S");

        gl.glRasterPos2d(ppm.getWestP().getPosX(), ppm.getWestP().getPosY());
        glut.glutBitmapString(GLUT.BITMAP_HELVETICA_10, "W");

        gl.glRasterPos2d(ppm.getEastP().getPosX(), ppm.getEastP().getPosY() - 0.025);
        glut.glutBitmapString(GLUT.BITMAP_HELVETICA_10, "E");

        gl.glColor3f(0, 1, 1);
        for (PolarProjectionMap.ConstellationName name : ppm.getConNames()) {
            if (name.isVisible()) {
                gl.glRasterPos2d(name.getPosX(), name.getPosY());
                glut.glutBitmapString(GLUT.BITMAP_HELVETICA_10, name.getName());
            }
        }

        //
        if ( ! disableMessier) {

            for (PolarProjectionMap.MessierData data : ppm.getMessierObjects()) {
                if (data.isVisible()) {
                    //gl.glBindTexture(GL.GL_TEXTURE_2D, texture[0]);
                    TextureHandler handler = textures.get(data.getName());
                    handler.enable();
                    handler.bind();

                    //System.out.printf("Id: %d\n", handler.getId());

                    float size = 0.1f;
                    gl.glBegin(GL2.GL_QUADS);
                    // Bottom left
                    gl.glTexCoord2f(0.0f, 0.0f);
                    gl.glVertex2f((float) data.getX(), (float) data.getY());

                    // Bottom Right
                    gl.glTexCoord2f(1.0f, 0.0f);
                    gl.glVertex2f((float) data.getX() + size, (float) data.getY());

                    // Upper right corner.
                    gl.glTexCoord2f(1.0f, 1.0f);
                    gl.glVertex2f((float) data.getX() + size, (float) data.getY() + size);

                    // Upper left corner.
                    gl.glTexCoord2f(0.0f, 1.0f);
                    gl.glVertex2f((float) data.getX(), (float) data.getY() + size);
                    gl.glEnd();
                    handler.disable();
                }
            }
        }
        gl.glColor3f(1.0f, 1.0f, 1.0f);
    }

    public void display(GLAutoDrawable canvas)
    {
        GL2 gl = canvas.getGL().getGL2();
        gl.glClear(GL.GL_COLOR_BUFFER_BIT);

        gl.glCallList(this.ppm_list);

        gl.glFlush();
    }

    public void reshape(GLAutoDrawable canvas, int left, int top, int width, int height)
    {
        GL2 gl = canvas.getGL().getGL2();
        gl.glViewport(0, 0, width, height);
        double ratio = (double) width / (double) height;
        gl.glMatrixMode(GLMatrixFunc.GL_PROJECTION);
        gl.glLoadIdentity();
        if (ratio < 1)
            gl.glOrtho(-v_size, v_size, -v_size, v_size / ratio, -1, 1);
        else
            gl.glOrtho(-v_size, v_size * ratio, -v_size, v_size, -1, 1);
        gl.glMatrixMode(GLMatrixFunc.GL_MODELVIEW);
    }

    public void displayChanged(GLAutoDrawable canvas, boolean modeChanged, boolean deviceChanged)
    {
        // pass
    }

    public void dispose(GLAutoDrawable arg0) {
        // pass
    }
}